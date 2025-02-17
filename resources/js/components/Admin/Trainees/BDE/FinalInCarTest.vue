<template>
  <div>
    <v-dialog v-model="finalTestDialog" width="600">
      <v-card>
        <div class="mx-5">
          <h3 class="text-h5 text-center py-3">
            {{ bde_log_id ? "Update" : "Add" }} Final Test Keys
          </h3>
          <div style="border: 1px solid black; border-top: 0px">
            <div :key="l" v-for="(final_test, l) in finalTestKeyLogs">
              <div
                style="
                  display: flex;
                  width: 100%;
                  border-top: 1px solid black;
                  background-color: lightgrey;
                "
              >
                <div style="width: 100%; padding-left: 5px">
                  {{ l + 1 }}. {{ final_test.title }}
                </div>
              </div>
              <div
                style="display: flex; width: 100%; border-top: 1px solid black"
                :key="m"
                v-for="(subtitle, m) in final_test.subtitles"
              >
                <div style="width: 100%; padding-left: 10px">
                  {{ subtitle["title"] }}
                  <div
                    class="float-right"
                    style="padding-left: 10px; margin-right: 10px"
                  >
                    <input
                      type="checkbox"
                      :checked="subtitle['mark_first']"
                      v-model="subtitle['mark_first']"
                    />
                    <input
                      type="checkbox"
                      :checked="subtitle['mark_second']"
                      v-model="subtitle['mark_second']"
                    />
                    <input
                      type="checkbox"
                      :checked="subtitle['mark_third']"
                      v-model="subtitle['mark_third']"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <div class="d-flex justify-content-between px-2 py-3">
            <v-btn
              class="text-white text-capitalize mr-3"
              color="red"
              @click="closeFinalTestDialog"
            >
              Close
            </v-btn>
            <v-btn
              class="text-white text-capitalize"
              color="green"
              @click="addFinalTestKeys"
            >
              {{ bde_log_id ? "Update" : "Add" }}
            </v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <p style="page-break-after: always"></p>
    <div style="width: 100%">
      <img
        src="/assets/media/logos/drivisa-logo200_80.svg"
        style="float: left; padding: 20px; display: flex; height: 60px"
        class="d-none"
      />
    </div>
    <div style="width: 100%; margin-bottom: 10px; padding-top: 50px">
      <div class="d-flex justify-content-between">
        <p style="text-align: center">
          <strong
            ><b>FINAL IN-CAR TEST</b>&emsp; 80% Minimum &emsp;&emsp; Y/N</strong
          >
        </p>
      </div>
      <div>
        <div class="my-5" v-for="(final_test_log, k) in bdeLog.final_test_log" :key="k">
         <div class="update-button-div">
           <v-btn
               class="text-capitalize primary btn-outline float-end mb-3"
               small
               @click="finalTestKeys(final_test_log, bdeLog.final_test_result[k]?.bde_log_id)"
           >
             {{ bdeLog.final_test_result[k]?.bde_log_id ? "Update" : "Add" }} Final Test Keys
           </v-btn>
         </div>
          <div :key="l" v-for="(final_test, l) in final_test_log" >
            <div
                style="
              display: flex;
              width: 100%;
              background-color: lightgrey;
            "
                class="custom-border"
            >
              <div style="width: 100%; padding-left: 5px;">
                {{ l + 1 }}. {{ final_test.title }}
              </div>
            </div>
            <div
                style="display: flex; width: 100%;"
                class="custom-border"
                :key="m"
                v-for="(subtitle, m) in final_test.subtitles"
            >
              <div style="width: 100%; padding-left: 10px" :class="m === final_test.subtitles.length - 1 ? 'custom-bottom-border' : ''">
                {{ subtitle["title"] }}
                <div
                    class="float-right"
                    style="padding-left: 10px; margin-right: 10px"
                >
                  <input
                      type="checkbox"
                      disabled
                      :checked="subtitle['mark_first']"
                  />
                  <input
                      type="checkbox"
                      disabled
                      :checked="subtitle['mark_second']"
                  />
                  <input
                      type="checkbox"
                      disabled
                      :checked="subtitle['mark_third']"
                  />
                </div>
              </div>
            </div>
          </div>

          <div style="display: flex; width: 100%; margin-top: 20px">
            <div style="width: 100%; background-color: #eae6e6; padding: 5px;">
              Final Test Mark:
              <span v-if="bdeLog.final_test_result[k]?.final_marks">
            {{ bdeLog.final_test_result[k].final_marks }}%
          </span>
              <span v-else> __ </span>
              &emsp;&emsp;&emsp;&emsp;
              {{ bdeLog.final_test_result[k]?.is_pass ? "Pass" : "Fail" }}
            </div>
          </div>
          <div
              style="display: flex; width: 100%; text-align: center; margin-top: 20px"
          >
            <div style="width: 100%">
              Instructor’s Name: {{ bdeLog.final_test_result[k]?.instructor_name }}
            </div>
            <div style="width: 100%">
              D.I. NO. {{ bdeLog.final_test_result[k]?.di_number }}
            </div>
          </div>

          <div style="display: flex; width: 100%; text-align: center">
            <div style="width: 100%">
              Test Date: {{ bdeLog.final_test_result[k]?.test_date }}
            </div>
            <div style="width: 100%">
              Instructor’s Signature:
              <img
                  :src="bdeLog.final_test_result[k]?.instructor_sign"
                  alt=""
                  width="80px"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="mt-5 d-none bottom-heading" style="text-align: center">
        <p><strong>www.drivisa.com</strong></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FinalInCarTest",
  props: ["bdeLog"],
  data() {
    return {
      finalTestDialog: false,
      final_test_logs: [],
      bde_log_id: null,
    };
  },
  computed: {
    finalTestKeyLogs() {
      let logs = [];
      this.final_test_logs.forEach((item) => {
        logs.push({
          title: item.title,
          subtitles: this.getSubtitles(item.subtitles),
        });
      });
      return logs;
    },
  },
  methods: {
    finalTestKeys(final_test_log, bde_log_id) {
      this.finalTestDialog = true;
      this.final_test_logs = final_test_log;
      this.bde_log_id = bde_log_id;
    },
    getSubtitles(subtitles) {
      let subs = [];
      subtitles.forEach((sub) => {
        subs.push({
          final_test_key_id: sub.id,
          title: sub.title,
          mark_first: sub.mark_first,
          mark_second: sub.mark_second,
          mark_third: sub.mark_third,
        });
      });
      return subs;
    },
    async addFinalTestKeys() {
      try {
        let url =
          "v1/drivisa/admin/bde/add-final-test-keys/" +
          this.$route.params.username;
        const { data } = await axios.post(url, {
          bde_log_id: this.bde_log_id,
          final_test_keys: this.finalTestKeyLogs,
        });
        this.finalTestDialog = false;
        this.$toasted.success("Success!");
        this.$emit("getBdeLog");
      } catch (e) {
        this.$toasted.error("Unable to update final test keys!");
      }
    },
    closeFinalTestDialog() {
      this.finalTestDialog = false;
      this.final_test_logs = [];
      this.bde_log_id = null;
    },
  },
};
</script>
<style scoped>
.custom-border {
  border-left: 1px solid black !important;
  border-right: 1px solid black !important;
  border-top: 1px solid black !important;
}

.custom-bottom-border {
  border-bottom: 1px solid black !important;
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
  img.img-fluid,
  img.mobile_logo,
  path,
  i,
  img{
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

  .bottom-heading {
    display: block !important;
  }
}
</style>