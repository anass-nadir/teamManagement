<template>
  <el-card class="box-card-component" style="margin-left:8px;">
    <div slot="header" class="box-card-header">
      <img src="https://wpimg.wallstcn.com/e7d23d71-cf19-4b90-a1cc-f56af8c0903d.png">
    </div>
    <div style="position:relative;">
        <el-row :gutter="8">
      <el-col :span="6">
      <pan-thumb :image="avatar!=null?avatar:''" class="panThumb"/>
       </el-col>
       <el-col :span="6">
      <avatar-upload :state="'edit'" />
       </el-col>
        </el-row>
      <mallki class-name="mallki-text" :text="user.firstName+' '+user.lastName"/>
   <el-form ref="dataForm" :rules="rules" :model="user" label-position="left" label-width="100px" style="width: 80%; margin-left:50px; padding-top:35px;">

        <el-form-item :label="$t('table.member.fname')" prop="firstName">
          <el-input v-model="user.firstName"/>
        </el-form-item>
         <el-form-item :label="$t('table.member.lname')" prop="lastName">
          <el-input v-model="user.lastName"/>
        </el-form-item>
         <el-form-item :label="$t('table.member.email')" prop="email">
          <el-input v-model="user.email"/>
        </el-form-item>
         <el-form-item :label="$t('table.member.password')" prop="password">
          <el-input v-model="user.password"/>
        </el-form-item>
        <el-button style="float: right;margin-bottom: 40px;" type="primary" @click="updateProfile">{{ $t('table.edit') }}</el-button>

      </el-form>

    </div>
  </el-card>
</template>

<script>
import PanThumb from '@/components/PanThumb'
import Mallki from '@/components/TextHoverEffect/Mallki'
import { validateEmail } from '@/utils/validate'
import AvatarUpload from '@/components/uploadAvatar/avatarUpload'
import { editMember } from '@/api/members'
export default {
  components: { PanThumb, Mallki,AvatarUpload },

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
       user:[],
rules: {
        email: [{ required: true, trigger: 'submit', validator: validateMail }],
        firstName: [{ required: true, message: 'first name is required', trigger: 'submit' }],
        lastName: [{ required: true, message: 'last name is required', trigger: 'submit' }],
        password: [{ required: true, validator: validatePassword, trigger: 'submit' }]
      },
    }
  },
  computed: {
    avatar () {
        return this.$store.state.user.avatar
    }
  },
  created() {
        this.user = this.$store.state.user.user
},

methods:{
         updateProfile() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          editMember(this.user).then(() => {
          this.$store.commit('SET_USER', this.user)
            this.$notify({
              title: 'Update Profile',
              message: 'Your Profile was updated successfully',
              type: 'success',
              duration: 2000
            })
          })
        }
      })
    }
}
}
</script>

<style rel="stylesheet/scss" lang="scss" >
.box-card-component{
  .el-card__header {
    padding: 0px!important;
  }
}
</style>
<style rel="stylesheet/scss" lang="scss" scoped>
.box-card-component {
  .box-card-header {
    position: relative;
    height: 220px;
    img {
      width: 100%;
      height: 100%;
      transition: all 0.2s linear;
      &:hover {
        transform: scale(1.1, 1.1);
        filter: contrast(130%);
      }
    }
  }
  .mallki-text {
    position: absolute;
    top: 0px;
    right: 0px;
    font-size: 20px;
    font-weight: bold;
  }
  .panThumb {
    z-index: 100;
    height: 70px!important;
    width: 70px!important;
    position: absolute!important;
    top: -45px;
    left: 0px;
    border: 5px solid #ffffff;
    background-color: #fff;
    margin: auto;
    box-shadow: none!important;
    /deep/ .pan-info {
      box-shadow: none!important;
    }
  }
  .progress-item {
    margin-bottom: 10px;
    font-size: 14px;
  }
  @media only screen and (max-width: 1510px){
    .mallki-text{
      display: none;
    }
  }
}
</style>
