<template>
  <v-card elevation="0" class="border">
    <v-card-text class="row">
      <!-- Licence    -->
      <DocumentUploaderComponent
          :document="getZoneObject('licence')"
          zone_name="licence"
          documentName="Licence"
      />
    </v-card-text>
  </v-card>
</template>

<script>
import DocumentUploaderComponent from "./DocumentUploaderComponent";
export default {
  name: "DocumentComponent",
  components: {DocumentUploaderComponent},
  data() {
    return {
      documents: []
    }
  },
  mounted() {
    this.getDocuments()
  },
  methods: {
    async getDocuments() {
      const url = '/v1/drivisa/trainees/documents';
      const {data} = await axios.get(url);
      this.documents = data.data;
    },
    getZoneObject(zone) {
      return this.documents.find(doc => doc.zone === zone);
    },
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
  max-height: 33%;
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
  width: 310px;
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