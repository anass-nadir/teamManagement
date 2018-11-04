<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input :placeholder="$t('table.member.fname')" v-model="listQuery.firstName" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>
      <el-input :placeholder="$t('table.member.lname')" v-model="listQuery.lastName" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>
      <el-input :placeholder="$t('table.member.email')" v-model="listQuery.email" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>

      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">{{ $t('table.search') }}</el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">{{ $t('table.add') }}</el-button>
      <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">{{ $t('table.export') }}</el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange">
      <el-table-column :label="$t('table.id')" prop="id" sortable="custom" align="center" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
            <el-table-column :label="$t('table.member.fname')" align="left" width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.firstName }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.member.lname')" align="left" width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.lastName }}</span>
        </template>
      </el-table-column>
       <el-table-column :label="$t('table.member.email')" align="left" min-width="200px">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100px">
        <template slot-scope="scope">
          <el-tag :type="scope.row.deleted_at==null?'success':'info'">{{ scope.row.deleted_at==null? 'publish': 'draft' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" width="230px" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleUpdate(scope.row)">{{ $t('table.edit') }}</el-button>
          <el-button v-if="scope.row.deleted_at!=null" size="mini" type="success" @click="handlePublishMember(scope.row)">{{ $t('table.publish') }}
          </el-button>
          <el-button v-if="scope.row.deleted_at==null" size="mini" @click="handleDelete(scope.row)">{{ $t('table.draft') }}
          </el-button>
          <el-button v-if="scope.row.deleted_at!=null" size="mini" type="danger" @click="handleDestroy(scope.row)">{{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="100px" style="width: 400px; margin-left:50px;">
        <el-form-item :label="$t('table.member.team')" prop="team_id">
          <el-select v-model="temp.team_id" class="filter-item" placeholder="Please select">
            <el-option v-for="item in teams" :key="item.id" :label="item.name" :value="item.id"/>
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('table.member.fname')" prop="firstName">
          <el-input v-model="temp.firstName"/>
        </el-form-item>
        <el-form-item :label="$t('table.member.lname')" prop="lastName">
          <el-input v-model="temp.lastName"/>
        </el-form-item>
        <el-form-item :label="$t('table.member.email')" prop="email">
          <el-input v-model="temp.email" type="email" />
        </el-form-item>
        <el-form-item :label="$t('table.member.password')" prop="password">
          <el-input v-model="temp.password"/>
        </el-form-item>
        <avatar-upload :state="dialogStatus" @avatarAdded="addAvatar" :exImage="temp.avatar!=null?temp.avatar:''" />
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">{{ $t('table.confirm') }}</el-button>
      </div>
    </el-dialog>


  </div>
</template>

<script>
import { fetchPage, draftMember, destroyMember, publishMember,getTeams, addMember, editMember } from '@/api/members'
import waves from '@/directive/waves' // Waves directive
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
import { validateEmail } from '@/utils/validate'
import AvatarUpload from '@/components/uploadAvatar/avatarUpload'


export default {
  name: 'Members',
  components: { Pagination, AvatarUpload },
  directives: { waves },
  data() {
       const validateMail = (rule, value, callback) => {
      if (!validateEmail(value)) {
        callback(new Error('Please enter a valid email'))
      } else {
        callback()
      }
    }
     const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error('The password can not be less than 6 digits'))
      } else {
        callback()
      }
    }
    return {
      tableKey: 0,
      list: null,
      teams: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        firstName: undefined,
        lastName: undefined,
        email: undefined,
        sort: '+id'
      },
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['published', 'draft'],
      temp: {
        id: undefined,
        firstName: '',
        lastName: '',
        email: '',
        avatar:'',
        password:'',
        team_id:'',
        deleted_at:null,
        status: 'published'
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create'
      },
      rules: {
        team_id: [{ required: true, message: 'please pick up a team', trigger: 'blur' }],
        email: [{ required: true, trigger: 'blur', validator: validateMail }],
        firstName: [{ required: true, message: 'first name is required', trigger: 'blur' }],
        lastName: [{ required: true, message: 'last name is required', trigger: 'blur' }],
        password: [{ required: true, validator: validatePassword, trigger: 'blur' }]
      },
      downloadLoading: false
    }
  },
  created() {
    this.getList()
  },
  methods: {
   async getList() {
      this.listLoading = true
     await fetchPage(this.listQuery).then(response => {
        this.list = response.data.members.data
        this.total = response.data.members.total

         getTeams().then(response => {
        this.teams = response.data.teams
         setTimeout(() => {
          this.listLoading = false
        }, 600)
         })

      })
    },
    handleFilter() {
      this.listQuery.page = 1
      this.getList()
    },
    addAvatar(avatar) {
    this.temp.avatar = avatar
    },
    handleModifyStatus(row, status) {
      this.$message({
        message: 'Status changed successfully',
        type: 'success'
      })
      row.deleted_at = status
    },
    sortChange(data) {
      const { prop, order } = data
      if (prop === 'id') {
        this.sortByID(order)
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id'
      } else {
        this.listQuery.sort = '-id'
      }
      this.handleFilter()
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        firstName: '',
        lastName: '',
        password:'',
        team_id:'',
        avatar:'',
        email: '',
        status: 'published'
      }
    },
    handleCreate() {
        this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          addMember(this.temp).then((data) => {
         this.temp.id = data.data.user
            this.list.unshift(this.temp)
            this.dialogFormVisible = false
            this.$notify({
              title: 'Add Member',
              message: 'Member was added successfully',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    },

    handleUpdate(row) {
      this.temp = Object.assign({}, row)
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
        updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp)
          editMember(tempData).then(() => {
            for (const v of this.list) {
              if (v.id === this.temp.id) {
                const index = this.list.indexOf(v)
                this.list.splice(index, 1, this.temp)
                break
              }
            }
            this.dialogFormVisible = false
            this.$notify({
              title: 'Update Member',
              message: 'Member was updated successfully',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    },
    handleDelete(row) {
         this.listLoading = true
      draftMember(row.id).then(response => {
        this.handleModifyStatus(row, response.data.date)
        setTimeout(() => {
          this.listLoading = false
        },200)
      }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Draft Member',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },
  handleDestroy(row) {
         this.listLoading = true
      destroyMember(row.id).then(response => {
       if(response.data.message=='success') {
         setTimeout(() => {
          this.listLoading = false
          this.$notify({
        title: 'Delete Member',
        message: 'Member Deleted successfully',
        type: 'success',
        duration: 2000 })
        },200)

      const index = this.list.indexOf(row)
      this.list.splice(index, 1)
      this.total--
      return

       }
        this.listLoading = false
          this.$notify({
        title: 'Delete Member',
        message: 'something wrong',
        type: 'error',
        duration: 2000 })
     }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Delete Member',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },

  handlePublishMember(row) {
         this.listLoading = true
      publishMember(row.id).then(response => {
       if(response.data.message=='success') {
         setTimeout(() => {
          this.listLoading = false
          this.handleModifyStatus(row, null)
        },200)
      return

       }
        this.listLoading = false
          this.$notify({
        title: 'Publish Member',
        message: 'something wrong',
        type: 'error',
        duration: 2000 })
     }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Delete Member',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['email', 'firstName', 'lastName']
        const filterVal = ['email', 'firstName', 'lastName']
        const data = this.formatJson(filterVal, this.list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list'
        })
        this.downloadLoading = false
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j])
        } else {
          return v[j]
        }
      }))
    }
  }
}
</script>
