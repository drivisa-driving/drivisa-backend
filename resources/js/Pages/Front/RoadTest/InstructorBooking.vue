<template>
  <div>
    <Header/>
    <div class="container">
      <div class="my-4">
        <div class="text-center">
          <h3><b>Make an Request</b></h3>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="field col-12 md:col-4 pt-2">
              <label for="location" class="text-black-100">Training Location:</label>
              <vue-google-autocomplete
                  ref="location"
                  id="location"
                  country="CA"
                  classname="form-control mb-3"
                  style="height: 45px"
                  placeholder="Address"
                  @placechanged="getAddressData"
                  v-model="requestData.location"
                  disabled
              >
              </vue-google-autocomplete>
            </div>
          </div>
          <!-- {{instructors}} -->

          <div class="col-md-6">
            <div class="field pt-2 col-12 md:col-12">
              <label class="text-black-100 ">Most Recent Instructors (that trained you):</label><br>
              <v-autocomplete
                  :items="instructors"
                  dense
                  outlined
                  label="Instructor"
                  item-text="fullName"
                  item-value="id"
                  v-model="requestData.instructor_id"
              >

              </v-autocomplete>
            </div>
          </div>

          <div class="col-md-12" v-show="requestData.instructor_id">
            <div class="field pt-2 col-12 md:col-12" v-if="currentPackage">
              <PickDrop :instructor_id="requestData.instructor_id"
                        :road_test="true"
                        :price="currentPackage.packageData.total"
                        @ok_btn="updatePickDrop"
              />
            </div>
          </div>

          <div class="pl-5">
            <h3 class="text-black-100 pt-3">Select Date and Time:</h3>
          </div>
          <div class="d-flex justify-content-between col-12 text-center btn mb-4 pt-3 pr-4">
            <Calendar class="w-100"
                      v-model="requestData.booking_date_time"
                      :showTime="true"
                      :inline="true"
                      :min-date="minDate"
                      dateFormat="YYYY-MM-DD"
            />
          </div>
          <div class="row pl-5 pr-5">
            <div class="text-center mb-4 pt-3 col-12 alert alert-warning text-black"> All times are in central
              time(CA).
            </div>
          </div>
        </div>
      </div>
      <div class="text-center pt-4">
        <button type="button" class="btn1" @click="registerRequest">Submit Request</button>
      </div>
    </div>
    <Footer/>
  </div>
</template>
<script>
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";
import VueGoogleAutocomplete from 'vue-google-autocomplete'
import moment from 'moment';
import PickDrop from "../../../components/Front/Booking/PickDrop";

export default {
  components: {Header, Footer, VueGoogleAutocomplete, PickDrop},
  created() {
    let today = new Date();
    let month = today.getMonth();
    let year = today.getFullYear();
    let nextMonth = (month === 11) ? 0 : month + 1;
    let nextYear = (nextMonth === 0) ? year + 1 : year;
    this.minDate = new Date();

    this.maxDate = new Date();
    this.maxDate.setMonth(nextMonth);
    this.maxDate.setFullYear(nextYear);
  },
  data() {
    return {
      packages: [],
      instructors: [],
      sourceName: null,
      minDate: null,
      requestData: {
        package_id: this.$route.query.package,
        booking_date_time: null,
        latitude: 44.2564394,
        longitude: -76.5643867,
        location: "381 Select Dr #1-5, Kingston, ON K7M 8R1, Canada",
        instructor_id: null,
        pick_drop: {
          type: 'default',
          drop_lat: null,
          drop_long: null,
          drop_address: null,
          pick_address: null,
          pick_lat: null,
          pick_long: null,
        }
      },
      term: null,
    }
  },
  mounted() {
    this.getInstructors();
    this.getRentalPackage();
  },
  computed: {
    currentPackage() {
      return this.packages.find(single_package => single_package.id = this.$route.query.package)
    }
  },
  methods: {
    async updatePickDrop(additionalCost, pickPoint, dropPoint, isSameAsPick) {
      if (isSameAsPick) {
        this.requestData.pick_drop = {
          type: "pick-drop",
          drop_lat: pickPoint.lat,
          drop_long: pickPoint.lng,
          pick_lat: pickPoint.lat,
          pick_long: pickPoint.lng,
          pick_address: pickPoint.address,
          drop_address: pickPoint.address
        }
      } else {
        this.requestData.pick_drop = {
          type: "pick-drop",
          drop_lat: dropPoint.lat,
          drop_long: dropPoint.lng,
          pick_lat: pickPoint.lat,
          pick_long: pickPoint.lng,
          pick_address: pickPoint.address,
          drop_address: dropPoint.address
        }
      }
    },
    async getInstructors() {
      let url = '/v1/drivisa/trainees/recent/instructors';
      const {data} = await axios.get(url);
      this.instructors = data.data
    },
    async getRentalPackage() {
      const {data} = await axios.get('/v1/drivisa/get-packages/rental')
      this.packages = data.data
    },
    getAddressData(addressData, placeResultData, id) {
      this.requestData.latitude = addressData.latitude;
      this.requestData.longitude = addressData.longitude;
      this.requestData.location = placeResultData.formatted_address
    },
    setTime(time) {
      this.requestData.booking_time = time;
    },
    async registerRequest() {
      try {
        let url = '/v1/drivisa/trainees/car-rental/register';


        this.requestData.booking_date = moment(this.requestData.booking_date).format('YYYY-MM-DD');
        this.requestData.booking_date_time = moment(this.requestData.booking_date_time).format('YYYY-MM-DD kk:mm:ss');

        const {data} = await axios.post(url, this.requestData)
        this.$toasted.success(data.message);
        await this.$router.push("/trainee/car-rental-requests")
      } catch (e) {
        console.log(e)
        this.$root.handleErrorToast(e, "Unable to register request");
      }
    }
  }
}
</script>

<style scoped lang="scss">
.p-fluid .p-autocomplete-input {
  padding: 10px !important;
}

.selected_btn {
  background: #4fe0b5 !important;
  color: black !important;
}

.button {
  width: 100px;
  background-color: #3f54d1;
  color: #fafafa;
  font-weight: bold;
}

.btn1 {
  width: 200px;
  height: 50px;
  background-color: #3f54d1;
  color: #fafafa;
}

section {
  margin-bottom: 45px;

  .filter_bar {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    grid-column-gap: 15px;
    text-align: center;
    padding: 5px;

    .d-form-field {
      flex: 1 1 100%;
      box-sizing: border-box;
      margin-right: 2rem;
      display: inline-block;
      position: relative;
      text-align: left;
      line-height: 1.125;
    }

    .filter_btn {
      display: flex;
      flex: 1 1 100%;
      place-content: center;
      align-items: center;
      min-height: 4rem;
      height: 4rem;

      btn {
        background-color: #3f54d1;
        color: #fafafa;
        margin: 0;
        min-width: 64px;
        line-height: 36px;
        padding: 0 16px;
        border-radius: 4px;

        span {
          cursor: pointer;
          white-space: nowrap;
          text-align: center;
          color: #fafafa;
          font-size: 14px;
          font-weight: 500;

          i {
            font-size: 18px;
            font-weight: bold;
            margin-left: 5px;
          }
        }
      }
    }
  }

  @media (max-width: 960px) {
    .filter_bar {
      display: flex;
      flex-direction: column;

      .d-form-field {
        margin-bottom: 0.5rem;
        max-height: 100%;
      }
    }
  }

  .instructor-cards {
    .d-card {
      box-sizing: border-box;
      display: flex;
      flex: 1 1 100%;
      max-width: 100%;
      box-shadow: 0 20px 105.701px rgba(51, 51, 51, .1);
      padding: 16px 11px;
      border-radius: 20px;
      align-items: center;
      cursor: pointer;
      background-color: #fff;

      .avatar {
        flex: 1 1 100%;
        box-sizing: border-box;
        max-width: 20%;
        text-align: center;

        img {
          min-width: 80px;
          min-height: 80px;
          border-radius: 50%;
          object-fit: cover;
          width: 40px;
          height: 40px;
          margin: 0;
          line-height: 40px;
          font-size: 17px;
          font-weight: 600;
          text-align: center;
        }
      }

      .content {
        flex-direction: column;
        box-sizing: border-box;
        display: flex;
        overflow-x: hidden;
        margin-inline: 20px;

        .name {
          font-size: 16px;
          font-weight: 700;
          margin: 0;
          text-align: left;
        }

        .address {
          align-items: center;
          text-align: left;

          span {
            font-size: 12px;
            font-weight: 800;

          }

          i {
            font-size: 17px;
            margin: 6px 0 0;
            background-repeat: no-repeat;
            display: inline-block;
            fill: currentColor;
            font-weight: 400 !important;
            line-height: 1;
          }
        }

        .car {
          text-align: left;
          align-items: center;
          color: gray;

          span {
            margin-left: 2px;
            white-space: nowrap;
            color: gray;
          }

          i {
            background-repeat: no-repeat;
            display: inline-block;
            fill: currentColor;
            font-size: 25px;
            vertical-align: middle;
            font-weight: 400 !important;
            line-height: 1;
            color: gray;
          }
        }

        .star_rating {
          text-align: left !important;

          i {
            color: orange;
            vertical-align: middle;
            fill: currentColor;
            font-size: 24px;
            margin-right: 19px;
          }
        }
      }
    }

    @media (max-width: 576px) {
      .d-card {
        padding: 10px 6px !important;
      }
    }
  }
}

</style>