<template>
  <div class="dashboard-editor-container">
       <el-row :gutter="8">
           <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 10}" :xl="{span: 10}" style="margin-bottom:30px;">
    <div class=" clearfix">
      <pan-thumb :image="avatar" style="float: left"> Your role:
        <span class="pan-info-roles">{{ roles }}</span>
      </pan-thumb>
      <div class="info-container">
        <span class="display_name">{{ name }}</span>
        <span style="font-size:20px;padding-top:20px;display:inline-block;">Members's Dashboard</span>
      </div>
    </div>
     </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 10}" :xl="{span: 10}" style="margin-bottom:30px;">
        <box-card/>
      </el-col>
    </el-row>
    <div>
      <img :src="emptyGif" class="emptyGif">
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import PanThumb from '@/components/PanThumb'
import BoxCard from '../admin/components/BoxCard'
export default {
  name: 'DashboardMember',
  components: { PanThumb,BoxCard },
  data() {
    return {
      emptyGif: 'https://wpimg.wallstcn.com/0e03b7da-db9e-4819-ba10-9016ddfdaed3'
    }
  },
  computed: {
    ...mapGetters([
      'name',
      'avatar',
      'roles'
    ])
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .emptyGif {
    display: block;
    width: 45%;
    margin: 0 auto;
  }

  .dashboard-editor-container {
    background-color: #e3e3e3;
    min-height: 100vh;
    padding: 50px 60px 0px;
    .pan-info-roles {
      font-size: 12px;
      font-weight: 700;
      color: #333;
      display: block;
    }
    .info-container {
      position: relative;
      margin-left: 190px;
      height: 150px;
      line-height: 200px;
      .display_name {
        font-size: 48px;
        line-height: 48px;
        color: #212121;
        position: absolute;
        top: 25px;
      }
    }
  }
</style>
