<template>
  <div class="components-container">

<pan-thumb v-if="state!='edit'" :image="image"/>

    <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">{{ state=='create'?'Add':'Change' }} Avatar
    </el-button>

    <image-cropper
      v-show="imagecropperShow"
      :width="300"
      :height="300"
      :key="imagecropperKey"
      url="https://httpbin.org/post"
      lang-type="en"
      @close="close"
      @crop-upload-success="cropSuccess"/>
  </div>
</template>

<script>
import ImageCropper from '@/components/ImageCropper'
import { uploadAvatar } from '@/api/members'
import PanThumb from '@/components/PanThumb'
export default {
  name: 'AvatarUpload',
   props: {
    state: {
      type: String,
      default: 'update'
    },
    exImage:{
        type: String,
        default: ' '
    }
  },
  components: { ImageCropper, PanThumb },
  computed:{
      image () {
       return this.exImage
      }
  },
  data() {
    return {
      imagecropperShow: false,
      imagecropperKey: 0
    }
  },
  methods: {
    cropSuccess(resData) {
      this.imagecropperShow = false
      this.imagecropperKey = this.imagecropperKey + 1
      if(this.state!='edit') {
          this.image = resData.files.avatar
          this.$emit('avatarAdded',resData.files.avatar)
      }
      else {
    let data = {
        userId: this.$store.state.user.user.id,
        img:resData.files.avatar
    }
        uploadAvatar(data).then((data)=> {
            this.$store.commit('SET_AVATAR', data.data.user.avatar)
        })
      }

    },
    close() {
      this.imagecropperShow = false
    }
  }
}
</script>

<style scoped>
  .avatar{
    width: 200px;
    height: 200px;
    border-radius: 50%;
  }
</style>

