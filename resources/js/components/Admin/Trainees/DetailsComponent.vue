<template>
  <div>
    <v-dialog v-model="changePasswordDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center pt-3">Change Password?</h3>
        <v-card-text class="text-center">
          <p>
            Once confirmed, the user will receive an email with a "New Password"
            that they can use to log into the application. Do you really want to
            proceed?
          </p>
        </v-card-text>
        <div class="d-flex justify-content-around px-2 py-3">
          <v-btn class="text-capitalize" @click="changePasswordDialog = false">
            No
          </v-btn>
          <v-btn
            class="text-white text-capitalize"
            color="green accent-5"
            @click="changeUserPassword"
          >
            Yes
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-card-text v-if="trainees != null">
      <div
        class="cover-section mb-1"
        :style="{ backgroundImage: 'url(' + trainees.cover + ')' }"
      >
        <div class="flex-box-start"></div>
        <div class="trainees-avatar">
          <img
            class="avatar"
            style="margin-bottom: 10px"
            :src="trainees.avatar"
            height="150px"
          />
          <h5 class="font-weight-bold mb-3 mt-3">{{ trainees.fullName }}</h5>
        </div>
      </div>

      <div style="margin-top: 32px">
        <div class="card">
          <TabView ref="tabview1">
            <TabPanel header="Details">
              <table class="table">
                <tr>
                  <td>Full Name</td>
                  <td>{{ trainees.fullName }}</td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td>{{ trainees.email }}</td>
                </tr>
                <tr>
                  <td>User Name</td>
                  <td>{{ trainees.username }}</td>
                </tr>
                <tr>
                  <td>No.</td>
                  <td>{{ trainees.no }}</td>
                </tr>
                <tr>
                  <td>Phone No.</td>
                  <td>{{ trainees.phoneNumber }}</td>
                </tr>
                <tr>
                  <td>Birth Date</td>
                  <td>{{ trainees.birthDate }}</td>
                </tr>
                <tr>
                  <td>Address:</td>
                  <td>{{ trainees.address }}</td>
                </tr>
                <tr>
                  <td>City</td>
                  <td>{{ trainees.city }}</td>
                </tr>
                <tr>
                  <td>Province</td>
                  <td>{{ trainees.province }}</td>
                </tr>
                <tr>
                  <td>Postal Code</td>
                  <td>{{ trainees.postalCode }}</td>
                </tr>
                <tr>
                  <td>Join Date</td>
                  <td>{{ trainees.createdAt }}</td>
                </tr>
                <tr>
                  <td>Hear From</td>
                  <td>{{ trainees.hearFrom }}</td>
                </tr>
              </table>
            </TabPanel>
            <TabPanel header="Lessons">
              <Trainings />
            </TabPanel>
            <TabPanel header="Car Booking">
              <CarRentals />
            </TabPanel>
            <TabPanel header="Courses">
              <Courses />
            </TabPanel>
            <TabPanel header="Purchases">
              <Purchases />
            </TabPanel>
            <TabPanel header="Refunds">
              <Refunds />
            </TabPanel>
            <TabPanel header="Documents">
              <div class="mt-3" v-if="trainees.documents?.length > 0">
                <div
                  v-for="document in trainees.documents"
                  :key="document.id"
                  class="mx-5 px-1 mb-3"
                >
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="mb-2">
                      <h5 class="font-weight-bold">
                        {{ document.zone.replace(/_/g, " ").toUpperCase() }}
                      </h5>
                      <img
                        :src="document.path"
                        alt=""
                        class="img-fluid"
                        width="200px"
                      />
                    </div>
                    <div>
                      <a
                        :href="document.path"
                        class="btn btn-primary btn-outline text-white"
                        download
                        target="_blank"
                        >Download
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="my-3 text-center font-weight-bold" v-else>
                No Document Uploaded Yet
              </div>
            </TabPanel>
            <TabPanel header="Actions">
              <v-tabs class="my-2">
                <v-tab> Send Message </v-tab>
                <v-tab> Change Password </v-tab>
                <v-tab-item>
                  <div>
                    <v-textarea
                      class="mt-3"
                      outlined
                      auto-grow
                      rows="4"
                      label="Message"
                      v-model="message.message"
                    ></v-textarea>
                    <button
                      class="btn btn-primary btn-outline ml-1 mb-1"
                      @click="sendNotification"
                    >
                      Send
                    </button>
                  </div>
                </v-tab-item>
                <v-tab-item>
                  <div>
                    <button
                      :disabled="btnDisabled"
                      class="btn btn-primary btn-outline ml-1 mb-1 mt-3"
                      @click="changePasswordDialog = true"
                    >
                      Generate Password
                    </button>
                  </div>
                </v-tab-item>
              </v-tabs>
            </TabPanel>
          </TabView>
        </div>
      </div>
    </v-card-text>
  </div>
</template>

<script>
import Trainings from "../../../components/Admin/Trainees/Details/Trainings.vue";
import Purchases from "../../../components/Admin/Trainees/Details/Purchases.vue";
import Courses from "../../../components/Admin/Trainees/Details/Courses.vue";
import Refunds from "../../../components/Admin/Trainees/Details/Refunds.vue";
import CarRentals from "../../../components/Admin/Trainees/Details/CarRentals/CarRentals.vue";
export default {
  name: "AdminProfile",
  components: { Trainings, Purchases, Courses, Refunds, CarRentals },
  data() {
    return {
      trainees: {},
      message: {
        message: null,
      },
      password: "",
      btnDisabled: false,
      changePasswordDialog: false,
    };
  },
  computed: {
    profile() {
      return this.$store.state.user.user;
    },
  },
  created() {
    this.gettrainees();
  },
  methods: {
    clearForm() {
      this.message = {
        message: null,
      };
    },
    updateFields() {
      this.btnDisabled = true;
      this.changePasswordDialog = false;
    },
    async gettrainees() {
      const { data } = await axios.get(
        "/v1/drivisa/admin/trainees/details/" + this.$route.params.id
      );
      this.trainees = data.data;
    },
    async sendNotification() {
      try {
        const url = "/v1/drivisa/admin/notification";
        const { data } = await axios.post(url, {
          selectedInstructors: [],
          selectedTrainees: [this.trainees.user.id],
          message: this.message.message,
        });
        this.$toasted.success("Notification Sent Successfully");
        this.clearForm();
      } catch (e) {
        this.$toasted.error("Can't Send Notification");
      }
    },

    async changeUserPassword() {
      try {
        const url = "/v1/user/change-user-password/" + this.trainees.user.id;
        const { data } = await axios.post(url);
        this.$toasted.success(data.message);
        this.updateFields();
      } catch (e) {
        this.$toasted.error("Unable to update password");
      }
    },
  },
};
</script>