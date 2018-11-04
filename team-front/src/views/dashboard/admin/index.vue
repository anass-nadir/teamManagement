<template>
  <div class="dashboard-editor-container">


    <el-row :gutter="8">
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6}" :xl="{span: 6}" style="margin-bottom:30px;">
        <box-card/>
        <!-- <avatar-upload /> -->
      </el-col>
    </el-row>

  </div>
</template>

<script>
import BoxCard from './components/BoxCard'
import AvatarUpload from '@/components/uploadAvatar/avatarUpload'

export default {
  name: 'DashboardAdmin',
  components: {
    BoxCard,
    AvatarUpload
  },
  data() {
    return {
    }
  },
  methods: {

  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}
</style>
