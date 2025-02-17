<template>
  <div>
    <MasterAdminLayout>
      <v-dialog v-model="markingKeysDialog" width="500">
        <v-card>
          <div class="mx-5 pt-3">
            <div v-for="marking in markings" :key="marking.id">
              <div class="px-3 pt-2 my-2 border rounded">
                <span
                  ><strong>{{ marking.title }}</strong></span
                >
                <v-radio-group v-model="marking.mark" row class="p-0 m-0 mt-2">
                  <v-radio
                    v-for="(option, i) in options"
                    :label="option.label"
                    :key="i"
                    :value="option.value"
                  ></v-radio>
                </v-radio-group>
              </div>
            </div>
          </div>
          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="d-flex justify-content-between px-2 py-3">
              <v-btn
                class="text-white text-capitalize mr-3"
                color="red"
                @click="closeDialogMethod"
              >
                Close
              </v-btn>
              <v-btn
                class="text-white text-capitalize"
                color="green"
                @click="addMarkingKeys"
              >
                Add Markings
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <div style="width: 100%" class="action-area">
        <div class="d-flex justify-content-end">
          <div
            class="p-1 d-flex justify-content-between"
            style="width: 30%; border-radius: 5px; border: 0.5px solid #cfcfcf"
          >
            <input
              type="number"
              placeholder="enter lesson no."
              style="outline: none; border: none"
              v-model="lesson_no"
            />
            <v-btn
              class="text-capitalize primary ml-1"
              small
              :disabled="lesson_no === ''"
              :class="{ 'btn-outline': lesson_no !== '' }"
              @click="addMarkings"
            >
              Add Markings
            </v-btn>
          </div>
          <v-btn
            @click="printForm()"
            class="text-capitalize primary btn-outline ml-2 print-form-btn"
            color="primary"
          >
            PRINT
          </v-btn>
        </div>
      </div>

      <div
        v-if="this.emptySignBde?.length > 0"
        class="d-flex justify-content-end align-items-center mt-2 sign-container"
      >
        <div
          style="border: 1px solid #9e9e9e; border-radius: 5px; width: 37%"
          class="p-2 sign-container"
        >
          <label for="trainee_signature" style="font-size: 12px"
            >Trainee Signature</label
          >
          <VueSignaturePad
            style="width: 80%; height: 100px"
            ref="traineeSignaturePad"
            id="trainee_signature"
            v-model="traineeSignature"
          />
          <v-btn
            @click="addSignature"
            small
            class="text-capitalize primary btn-outline ml-2 print-form-btn float-end"
            color="primary"
          >
            Add Sign
          </v-btn>
        </div>
      </div>

      <div>
        <StudentInCarRecord :bdeLog="bdeLog" />
        <MarkingKeyComponent :bdeLog="bdeLog" />
        <FinalInCarTest :bdeLog="bdeLog" @getBdeLog="getBdeLog" />
      </div>
    </MasterAdminLayout>
  </div>
</template>
<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import StudentInCarRecord from "../../../components/Admin/Trainees/BDE/StudentInCarRecord";
import MarkingKeyComponent from "../../../components/Admin/Trainees/BDE/MarkingKeyComponent";
import FinalInCarTest from "../../../components/Admin/Trainees/BDE/FinalInCarTest";

export default {
  name: "BDELog",
  components: {
    FinalInCarTest,
    StudentInCarRecord,
    MasterAdminLayout,
    MarkingKeyComponent,
  },
  data() {
    return {
      bdeLog: {},
      lesson_no: "",
      markingKeys: [],
      options: [
        { label: "E", value: 1 },
        { label: "M", value: 2 },
        { label: "P", value: 3 },
        { label: "N", value: 4 },
      ],
      markings: [],
      markingKeysDialog: false,
      emptySignBde: [],
      traineeSignature: null,
    };
  },
  created() {
    this.getBdeLog();
    this.getEmptySignBdeLog();
  },
  methods: {
    printForm() {
      window.print();
    },
    async getBdeLog() {
      const { data } = await axios.get(
        "/v1/drivisa/bde/trainee/" + this.$route.params.username
      );
      this.bdeLog = data.data;
    },
    addMarkings() {
      this.getMarkingKeys();
      this.markingKeysDialog = true;
    },
    closeDialogMethod() {
      this.markingKeysDialog = false;
      this.lesson_no = "";
      this.markingKeys = [];
      this.markings = [];
    },
    async getMarkingKeys() {
      try {
        let url = "/v1/drivisa/admin/bde/get-marking-keys";
        const { data } = await axios.get(url);
        this.markingKeys = data;
        this.markingKeys = data.data;

        this.markings = [];
        data.data.forEach((item) => {
          this.markings.push({
            marking_key_id: item.id,
            title: item.title,
            mark: null,
          });
        });
      } catch (e) {}
    },
    async addMarkingKeys() {
      try {
        let url = "v1/drivisa/admin/bde/add-marking-keys";
        const { data } = await axios.post(url, {
          lesson_no: this.lesson_no,
          fillMarkingKeys: true,
          markings: this.markings,
        });
        this.closeDialogMethod();
        this.getBdeLog();
        this.$toasted.success(data.message);
      } catch (e) {
        this.$toasted.error(e.response.data.message);
      }
    },
    async getEmptySignBdeLog() {
      const { data } = await axios.get(
        "/v1/drivisa/admin/bde/empty-sign-logs/" + this.$route.params.username
      );
      this.emptySignBde = data;
    },
    async addSignature() {
      try {
        const signaturePad = this.$refs.traineeSignaturePad.signaturePad;
        const signatureData = signaturePad
          .toDataURL()
          .replace(/^data:image\/\w+;base64,/, "");

        let url = `v1/drivisa/admin/bde/add-trainee-sign/${this.$route.params.username}`;
        const { data } = await axios.post(url, {
          signatureData: signatureData,
        });
        this.$toasted.success(data.message);
        location.reload();
      } catch (e) {
        this.$toasted.error(e.response.data.message);
      }
    },
  },
};
</script>
<style lang="scss" scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}
@media print {
  .header,
  .footer-bg,
  .v-btn__content i.v-icon,
  .v-btn__content .v-icon,
  .print-form-btn,
  .action-area,
  .sign-container,
  .update-button-div,
  input,
  v-btn,
  img.img-fluid{
    display: none !important;
  }

  a[href]:after {
    content: none !important;
  }

  @page {
    margin-top: 10mm;
    margin-bottom: 10mm;
  }
  body {
    padding-top: 72px;
    padding-bottom: 72px;
  }
}
</style>