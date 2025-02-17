<template>
  <div class="col-md-6">
    <div class="document-box">
      <h3>{{ documentName }}</h3>
      <div>
        <div class="d-none">
          <input type="file" @change="setFilePath"
                 :ref="zone_name"
                 :id="zone_name+'_file'"
                 class="d-none"
          >
        </div>
        <div
            class="my-2"
            v-if="thumb || preview"
            @click="loadInput"
        >
          <DocumentStatus :document="document"/>
          <div class="upload-box">
            <img :src="thumb"
                 :alt="documentName"
                 ref="img_tag"
                 style="width: 360px;height: 180px;object-fit: contain"
            >
          </div>
        </div>
        <div class="upload-box"
             @click="loadInput"
             v-else>
          <div>
            <i class="upload-icon mdi mdi-upload"></i>
          </div>
          <div>
            <strong>Upload {{ documentName }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DocumentStatus from "./DocumentStatus";

export default {
  name: "DocumentUploaderComponent",
  components: {DocumentStatus},
  props: ['document', 'zone_name', 'documentName'],
  data() {
    return {
      file: null,
      preview: false,
    }
  },
  computed:{
    thumb(){
      let url = null;
      if (this.document){
        url = this.document.thumb
      }
      return url;
    }
  },
  methods: {
    setFilePath() {
      this.file = this.$refs[this.zone_name].files[0];
      const url = URL.createObjectURL(this.$refs[this.zone_name].files[0])


      let image_element = this.$refs.img_tag;
      image_element.style.width = '360px';
      image_element.style.height = '180px';
      image_element.style.objectFit = 'contain';
      image_element.src = url;

      this.uploadPhotoToServer();
    },
    loadInput() {
      this.preview = true;
      this.$refs[this.zone_name].click()
    },
    async uploadPhotoToServer() {
      try {
        let url = "/v1/drivisa/trainees/documents/single";
        const formData = new FormData();
        formData.append(`file`, this.file);
        formData.append(`zone`, this.zone_name);

        const {data} = await axios.post(url, formData);

        this.$toasted.success(this.documentName + " is Uploaded")

      } catch (e) {
        this.$toasted.error(this.documentName + " not uploaded successfully")
      }
    }
  }
}
</script>

<style scoped>
.document-box {
  margin-bottom: 20px;
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  place-content: stretch center;
  align-items: stretch;
  flex: 1 1 33%;
}

.document-box h3 {
  font: 400 24px/32px Montserrat, sans-serif !important;
  letter-spacing: normal;
  margin: 0 0 16px;
  color: rgba(0, 0, 0, .87);
}

.upload-box {
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  place-content: center;
  align-items: center;
  background-color: #eee;
  min-width: 309px;
  max-width: 360px;
  height: 180px;
  cursor: pointer;
  border: 1px dashed silver;
  border-radius: 2px;
}

.upload-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  color: #008a2e;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
}
</style>