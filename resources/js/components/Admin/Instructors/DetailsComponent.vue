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

    <v-card-text v-if="instructor != null">
      <div
        class="cover-section mb-1"
        :style="{ backgroundImage: 'url(' + getCoverPhoto + ')' }"
      >
        <div class="flex-box-start"></div>
        <div class="instructor-avatar">
          <img
            class="avatar"
            style="margin-bottom: 10px"
            :src="instructor.avatar"
          />
          <h5 class="font-weight-bold mb-3 mt-3">{{ instructor.fullName }}</h5>
        </div>
      </div>

      <div style="margin-top: 32px">
        <div class="card">
          <TabView ref="tabview1">
            <TabPanel header="Details">
              <table class="table">
                <tr>
                  <td>Full Name</td>
                  <td>{{ instructor.fullName }}</td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td>{{ instructor.email }}</td>
                </tr>
                <tr>
                  <td>User Name</td>
                  <td>{{ instructor.username }}</td>
                </tr>
                <tr>
                  <td>Instructor No.</td>
                  <td>{{ instructor.no }}</td>
                </tr>
                <tr>
                  <td>Phone No.</td>
                  <td>{{ instructor.phoneNumber }}</td>
                </tr>
                <tr>
                  <td>Birth Date</td>
                  <td>{{ instructor.birthDate }}</td>
                </tr>
                <tr>
                  <td>Address:</td>
                  <td>{{ instructor.address }}</td>
                </tr>
                <tr>
                  <td>City</td>
                  <td>{{ instructor.city }}</td>
                </tr>
                <tr>
                  <td>Province</td>
                  <td>{{ instructor.province }}</td>
                </tr>
                <tr>
                  <td>Postal Code</td>
                  <td>{{ instructor.postalCode }}</td>
                </tr>
                <tr>
                  <td>Languages</td>
                  <td>{{ instructor.languages }}</td>
                </tr>
                <tr>
                  <td>Hear From</td>
                  <td>{{ instructor.hearFrom }}</td>
                </tr>
                <tr>
                  <td>Driving Licence Number</td>
                  <td>{{ instructor.licenceNumber }}</td>
                </tr>
                <tr>
                  <td>Driving Licence End Date</td>
                  <td>{{ instructor.licenceEndDate }}</td>
                </tr>
                <tr>
                  <td>DI Licence Number</td>
                  <td>{{ instructor.diNumber }}</td>
                </tr>
                <tr>
                  <td>DI End</td>
                  <td>{{ instructor.diEndDate }}</td>
                </tr>
                <tr>
                  <td>Join Date</td>
                  <td>{{ instructor.createdAt }}</td>
                </tr>
                <tr>
                  <td>Agreement Signed Date</td>
                  <td>{{ instructor.signed_at }}</td>
                </tr>
                <tr>
                  <td>Stripe Account ID</td>
                  <td>{{ instructor.stripe_account_id }}</td>
                </tr>
              </table>
            </TabPanel>
            <TabPanel header="Lessons">
              <Trainings :totalHours="instructor.lessons" />
            </TabPanel>
            <TabPanel header="Car Booking">
              <CarRentals />
            </TabPanel>
            <TabPanel header="Schedules">
              <Schedules />
            </TabPanel>
            <TabPanel header="Reviews">
              <div class="mb-3 d-flex justify-content-end">
                <h6>
                  Average: <strong>{{ instructor.evaluation?.avg }}</strong>
                </h6>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <h6>
                  <strong>{{ instructor.evaluation?.count }}</strong>
                  Reviews
                </h6>
              </div>
              <Reviews :reviews="instructor.evaluation?.comments" />
            </TabPanel>
            <TabPanel header="Earnings">
              <Earnings />
            </TabPanel>
            <TabPanel header="Documents">
              <div>
                <!--  Driver licence    -->
                <DocumentDetails
                  :document="getZoneObject('driver_licence')"
                  zone_name="driver_licence"
                  documentName="Driver's Licence"
                />

                <!--  Vehicle insurance policy  (Liability Slip)  -->
                <DocumentDetails
                  :document="
                    getZoneObject('vehicle_insurance_policy_liability_slip')
                  "
                  zone_name="vehicle_insurance_policy_liability_slip"
                  documentName="Vehicle Insurance Policy (Liability Slip) "
                />

                <!--  Vehicle insurance policy  (OPCF 6D)  -->
                <DocumentDetails
                  :document="getZoneObject('vehicle_insurance_policy_opcf_6d')"
                  zone_name="vehicle_insurance_policy_opcf_6d"
                  documentName="Vehicle Insurance Policy (OPCF 6D)"
                />

                <!--  Vehicle Registration    -->
                <DocumentDetails
                  :document="getZoneObject('vehicle_registration')"
                  zone_name="vehicle_registration"
                  documentName="Vehicle Registration "
                />

                <!--  Safety for vehicle    -->
                <DocumentDetails
                  :document="getZoneObject('safety_vehicle')"
                  zone_name="safety_vehicle"
                  documentName="Safety For Vehicle"
                />

                <!--  Driving instructor license   -->
                <DocumentDetails
                  :document="getZoneObject('driving_instructor_license')"
                  zone_name="driving_instructor_license"
                  documentName="Driving Instructor License"
                />

                <!--  Dual brake and license plate   -->
                <DocumentDetails
                  :document="getZoneObject('dual_brake')"
                  zone_name="dual_brake"
                  documentName="Dual Brake Photo"
                />

                <!--  Dual brake and license plate   -->
                <DocumentDetails
                  :document="
                    getZoneObject('front_picture_of_car_showing_the_plate')
                  "
                  zone_name="front_picture_of_car_showing_the_plate"
                  documentName="Front picture Of Car Showing The Plate"
                />

                <!--  college Certificate   -->
                <DocumentDetails
                  :document="getZoneObject('college_certificate')"
                  zone_name="college_certificate"
                  documentName="College Certificate"
                />

                <!--   Business License-->
                <DocumentDetails
                  :document="getZoneObject('instructor_business_license')"
                  zone_name="instructor_business_license"
                  documentName="Instructor Business License (if required by the city)"
                />

                <!--   Car Business License-->
                <DocumentDetails
                  :document="getZoneObject('car_business_license')"
                  zone_name="car_business_license"
                  documentName="Car Business License (if required by the city)"
                />
              </div>
            </TabPanel>
            <TabPanel header="Locations">
              <Locations />
            </TabPanel>
            <TabPanel header="Actions">
              <v-tabs left>
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
                      class="btn btn-primary btn-outline mt-3 ml-1 mb-1"
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
import Trainings from "../../../components/Admin/Instructors/Details/Trainings.vue";
import Earnings from "../../../components/Admin/Instructors/Details/Earnings.vue";
import Schedules from "./Details/Schedules/Schedules.vue";
import DocumentStatus from "../../../components/Admin/Instructors/Details/DocumentStatus.vue";
import DocumentDetails from "../../../components/Admin/Instructors/Details/DocumentDetails.vue";
import CarRentals from "../../../components/Admin/Instructors/Details/CarRentals/CarRentals.vue";
import Reviews from "../../../components/Admin/Instructors/Details/Reviews.vue";
import Locations from "../../../components/Admin/Instructors/Details/Locations.vue";
export default {
  name: "AdminProfile",
  components: {
    Trainings,
    Earnings,
    Schedules,
    DocumentStatus,
    DocumentDetails,
    CarRentals,
    Reviews,
    Locations
  },
  data() {
    return {
      instructor: {},
      documents: [],
      message: {
        message: null,
      },
      user: {
        first: "",
        last: "",
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
    getCoverPhoto() {
      let documents = this.instructor.documents
        ? this.instructor.documents.length
        : 0;
      if (documents > 0) {
        let docs = this.instructor.documents;
        let doc = docs.find((document) => {
          return document.zone === "front_picture_of_car_showing_the_plate";
        });
        if (doc != undefined) {
          return doc.path;
        } else {
          return this.instructor.cover;
        }
      } else {
        return this.instructor.cover;
      }
    },
  },
  created() {
    this.getInstructor();
  },
  mounted() {
    this.getDocuments();
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
    async getInstructor() {
      const { data } = await axios.get(
        "/v1/drivisa/admin/instructors/details/" + this.$route.params.id
      );
      this.instructor = data.data;
    },
    async getDocuments() {
      const url =
        "/v1/drivisa/admin/instructors/" + this.$route.params.id + "/documents";
      const { data } = await axios.get(url);
      this.documents = data.data;
    },
    getZoneObject(zone) {
      return this.documents.find((doc) => doc.zone === zone);
    },
    async sendNotification() {
      try {
        const url = "/v1/drivisa/admin/notification";
        const { data } = await axios.post(url, {
          selectedInstructors: [this.instructor.user.id],
          selectedTrainees: [],
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
        const url = "/v1/user/change-user-password/" + this.instructor.user.id;
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