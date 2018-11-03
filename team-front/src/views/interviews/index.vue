<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input :placeholder="$t('table.interview.synthesis')" v-model="listQuery.synthesis" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>
      <el-input :placeholder="$t('table.interview.place')" v-model="listQuery.place" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>
      <el-input :placeholder="$t('table.interview.subject')" v-model="listQuery.subject" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter"/>

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
            <el-table-column :label="$t('table.interview.synthesis')" align="left" min-width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.synthesis }}</span>
        </template>
      </el-table-column>
       <el-table-column :label="$t('table.interview.subject')" align="left" width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.subject }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.interview.place')" align="left" width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.place }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.interview.users')" align="left" width="200px">
        <template slot-scope="scope">
          <span v-for="(item,index) in scope.row.users" :key="index" style="display:inline-block">[{{ item.firstName }} {{ item.lastName }}]</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100px">
        <template slot-scope="scope">
          <el-tag :type="scope.row.deleted_at==null?'success':'info'">{{ scope.row.deleted_at==null? 'publish': 'draft' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" width="230px" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button type="primary" size="mini">{{ $t('table.edit') }}</el-button>
          <el-button v-if="scope.row.deleted_at!=null" size="mini" type="success" @click="handlePublishInterview(scope.row)">{{ $t('table.publish') }}
          </el-button>
          <el-button v-if="scope.row.deleted_at==null" size="mini" @click="handleDelete(scope.row)">{{ $t('table.draft') }}
          </el-button>
          <el-button v-if="scope.row.deleted_at!=null" size="mini" type="danger" @click="handleDestroy(scope.row)">{{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" width="80%">
        <el-row :gutter="20">
             <el-col :span="12">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="80px" style="width: 400px; margin-left:50px;">

        <el-form-item :label="$t('table.interview.subject')" prop="subject">
          <el-input v-model="temp.subject"/>
        </el-form-item>
         <el-form-item :label="$t('table.interview.synthesis')" prop="synthesis">
          <el-input v-model="temp.synthesis"/>
        </el-form-item>
         <el-form-item :label="$t('table.interview.place')" prop="place">
          <el-input v-model="temp.place"/>
        </el-form-item>

      </el-form>
       </el-col>
        <el-col :span="12">
 <pick-member @memberAdded="memberAdded" @memberRemoved="memberRemoved" />
             </el-col>
      </el-row>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">{{ $t('table.confirm') }}</el-button>
      </div>
    </el-dialog>


  </div>
</template>

<script>
import { fetchPage, draftInterview, destroyInterview, publishInterview, createInterview } from '@/api/interviews'
import waves from '@/directive/waves' // Waves directive
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
import PickMember from '@/components/PickMember'
const calendarTypeOptions = [
  { key: 'CN', display_name: 'China' },
  { key: 'US', display_name: 'USA' },
  { key: 'JP', display_name: 'Japan' },
  { key: 'EU', display_name: 'Eurozone' }
]

// arr to obj ,such as { CN : "China", US : "USA" }
const calendarTypeKeyValue = calendarTypeOptions.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name
  return acc
}, {})

export default {
  name: 'Interviews',
  components: { Pagination, PickMember },
  directives: { waves },
  filters: {
    typeFilter(type) {
      return calendarTypeKeyValue[type]
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        synthesis: undefined,
        place: undefined,
        subject: undefined,
        sort: '+id'
      },
      importanceOptions: [1, 2, 3],
      calendarTypeOptions,
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['published', 'draft'],
      temp: {
        id: undefined,
        users: [],
        synthesis: '',
        place: '',
        subject: '',
        status: 'published'
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create'
      },
      dialogPvVisible: false,
      pvData: [],
      rules: {
        subject: [{ required: true, message: 'subject is required', trigger: 'blur' }],
        synthesis: [{required: true, message: 'synthesis is required', trigger: 'blur' }],
        place: [{ required: true, message: 'place is required', trigger: 'blur' }]
      },
      downloadLoading: false
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      fetchPage(this.listQuery).then(response => {
        this.list = response.data.interviews.data
        this.total = response.data.interviews.total

        // Just to simulate the time of the request
        setTimeout(() => {
          this.listLoading = false
        }, 600)
      })
    },
    handleFilter() {
      this.listQuery.page = 1
      this.getList()
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
        users: [],
        synthesis: '',
        place: '',
        subject: '',
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
    memberAdded (member) {
     this.temp.users.push(member)
    },
    memberRemoved (member) {
        this.temp.users.indexOf(member) !== -1 && this.temp.users.splice(this.temp.users.indexOf(member), 1)
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
          if(this.temp.users.length==0) {
              this.$notify({
              title: 'Add Interview',
              message: 'Please pick up at least one member',
              type: 'error',
              duration: 2000
            })
            return
          }
        if (valid) {
          createInterview(this.temp).then((data) => {
         this.temp.id = data.data.interview
            this.list.unshift(this.temp)
            this.dialogFormVisible = false
            this.$notify({
              title: 'Add Interview',
              message: 'Interview was added successfully',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.temp.timestamp = new Date(this.temp.timestamp)
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
/*     updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp)
          tempData.timestamp = +new Date(tempData.timestamp) // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          updateArticle(tempData).then(() => {
            for (const v of this.list) {
              if (v.id === this.temp.id) {
                const index = this.list.indexOf(v)
                this.list.splice(index, 1, this.temp)
                break
              }
            }
            this.dialogFormVisible = false
            this.$notify({
              title: '成功',
              message: '更新成功',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    }, */
    handleDelete(row) {
         this.listLoading = true
      draftInterview(row.id).then(response => {
        /* this.list = response.data.data
        this.total = response.data.total */
        this.handleModifyStatus(row, response.data.date)
        // Just to simulate the time of the request
        setTimeout(() => {
          this.listLoading = false
        },200)
      }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Draft Interview',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },
  handleDestroy(row) {
         this.listLoading = true
      destroyInterview(row.id).then(response => {
       if(response.data.message=='success') {
         setTimeout(() => {
          this.listLoading = false
          this.$notify({
        title: 'Delete Interview',
        message: 'Interview Deleted successfully',
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
        title: 'Delete Interview',
        message: 'something wrong',
        type: 'error',
        duration: 2000 })
     }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Delete Interview',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },

  handlePublishInterview(row) {
         this.listLoading = true
      publishInterview(row.id).then(response => {
       if(response.data.message=='success') {
         setTimeout(() => {
          this.listLoading = false
          this.handleModifyStatus(row, null)
        },200)
      return

       }
        this.listLoading = false
          this.$notify({
        title: 'Publish Interview',
        message: 'something wrong',
        type: 'error',
        duration: 2000 })
     }).catch((err)=> {
          this.listLoading = false
          this.$notify({
        title: 'Delete Interview',
        message: err,
        type: 'error',
        duration: 2000 })
     })
    },
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['subject', 'synthesis', 'place']
        const filterVal = ['subject', 'synthesis', 'place']
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
