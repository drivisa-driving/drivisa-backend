<template>
  <div>
    <div class="d-none">
      <input type="file"
             @change="setProfilePhoto('profile_picture')"
             ref="profile_picture"
             id="profile_picture">
      <input type="file"
             @change="setProfilePhoto('cover_picture')"
             ref="cover_picture"
             id="cover_picture">
    </div>
    <v-card elevation="0" class="profile_section" v-if="profile != null">
      <div class="cover-section mb-1" :style="{ backgroundImage: 'url(' + profile.cover + ')' }">
        <div class="flex-box-start"></div>
        <div class="instructor-avatar">
          <div class="position-relative">
            <img class="avatar"
                 :src="profile.avatar"
                 onclick="document.querySelector('#profile_picture').click()">
            <span
                v-if="showDPDeleteBtn"
                class="position-absolute mdi mdi-delete text-danger"
                style="bottom: 0;
                    width: 40px;
                    height: 40px;
                    font-size: 24px;
                    border-radius: 50%;
                    color:white;
                     background: #0f1317;
                     padding: 3px;
                     cursor:pointer;
                     right: 0"
            >
              <v-btn
                  text
                  @click="removePhoto('profile_picture')"
              ></v-btn>
            </span>
          </div>
          <h5 class="font-weight-bold my-2">{{ profile.fullName }}</h5>
          <div class="car">
            <i class="car-icon mdi mdi-car"></i>
            <span v-if="car != null">{{ car.generation }} {{ car.make }}</span>
            <span v-else>Not Provided</span>
          </div>
          <div class="star_rating">
            <div class="star-rating d-flex justify-content-center">
              <v-rating
                  v-model="profile.evaluation.avg"
                  readonly
                  background-color="orange darken"
                  color="orange"
                  icon-label="custom icon label text"
              ></v-rating>
            </div>
            <small>
              {{ profile.evaluation.comments.length }}
              <span v-if="profile.evaluation.comments.length < 2">comment</span>
              <span v-else>comments</span>
            </small>
          </div>
        </div>
        <div class="d-none">
          <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
                  <span
                      class="edit-profile-btn"
                      v-bind="attrs"
                      v-on="on"
                  >
                    <i class="mdi mdi-camera"></i>
                    <span>Upload Your Vehicle Photo</span>
                  </span>
            </template>
            <v-list>
              <v-list-item onclick="document.querySelector('#cover_picture').click()">
                <v-list-item-title>Change Photo</v-list-item-title>
              </v-list-item>
              <v-list-item @click="removePhoto('cover_picture')">
                <v-list-item-title>Remove Photo</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>
      </div>
      <v-card-text style="padding: 3rem">
        <div class="row mt-5">
          <div class="col-md-6">
            <ValidationProvider name="first name" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="First Name *"
                  placeholder="First Name"
                  v-model="profile.first_name"
              ></v-text-field>
            </ValidationProvider>
          </div>
          <div class="col-md-6">
            <ValidationProvider name="last name" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="Last Name *"
                  placeholder="Last Name * "
                  v-model="profile.last_name"
              >
              </v-text-field>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="email address" rules="email" v-slot="{ errors }">
              <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="Email Address *"
                  placeholder="Email Address * "
                  v-model="profile.email"
              ></v-text-field>
            </ValidationProvider>
          </div>
          <div class="col-md-6">
            <v-text-field
                outlined
                label="Phone Number *"
                placeholder="+1 (XXX) XXX-XXXXX * "
                v-maska="'+1 (###) ###-####'"
                v-model="profile.phone_number"
            >
            </v-text-field>
          </div>
          <div class="col-12">
            <vue-google-autocomplete
                ref="source_location"
                id="source_location"
                country="CA"
                classname="form-control mb-3"
                style="height: 56px"
                placeholder="Address"
                @placechanged="getAddressData"
                v-model="profile.address"
            >
            </vue-google-autocomplete>
          </div>
          <div class="col-md-6">
            <ValidationProvider name="street" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  disabled
                  :error-messages="errors[0]"
                  label="Street Name *"
                  placeholder="Street * "
                  v-model="profile.street"
              ></v-text-field>
            </ValidationProvider>
          </div>
          <div class="col-md-6">
            <ValidationProvider name="city" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  disabled
                  :error-messages="errors[0]"
                  label="City *"
                  placeholder="City * "
                  v-model="profile.city"
              ></v-text-field>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <v-menu
                ref="birthDate"
                v-model="birthDate"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="profile.birth_date"
                    label="Enter your birth date"
                    prepend-inner-icon="mdi-calendar"
                    outlined
                    v-bind="attrs"
                    v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="profile.birth_date"
                  no-title
                  scrollable
              >
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    @click="$refs.birthDate.save(profile.birth_date)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-menu>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="province" rules="required" v-slot="{ errors }">
              <v-select
                  :items="provinces"
                  item-text="name"
                  item-value="value"
                  outlined
                  label="Province *"
                  placeholder="Province * "
                  v-model="profile.province"
              ></v-select>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="postal code" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  disabled
                  :error-messages="errors[0]"
                  label="Postal Code *"
                  placeholder="A1A 1A1"
                  v-maska="'A#A #A#'"
                  v-model="profile.postal_code"
              ></v-text-field>
            </ValidationProvider>
          </div>


          <div class="col-md-6">
            <v-text-field
                outlined
                label="Unit No"
                placeholder="Unit No. Aka  Apartment/House No. "
                v-model="profile.unit_no"
            ></v-text-field>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="licence number" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="Driving Licence Number *"
                  placeholder="Driving Licence Number"
                  v-maska="'XXXXX-XXXXX-XXXXX'"
                  v-model="profile.licence_number"
              ></v-text-field>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="licence number expire date" rules="required" v-slot="{ errors }">
              <v-menu
                  ref="licenceEndDate"
                  v-model="licenceEndDate"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="profile.licence_end_date"
                      label="Driving Licence Expire Date"
                      prepend-inner-icon="mdi-calendar"
                      outlined
                      v-bind="attrs"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="profile.licence_end_date"
                    no-title
                    scrollable
                >
                  <v-spacer></v-spacer>
                  <v-btn
                      text
                      color="primary"
                      @click="$refs.licenceEndDate.save(profile.licence_end_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="DI number" rules="required" v-slot="{ errors }">
              <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="DI Licence Number *"
                  placeholder="DI Licence Number"
                  v-model="profile.di_number"
              ></v-text-field>
            </ValidationProvider>
          </div>

          <div class="col-md-6">
            <ValidationProvider name="DI number expire date" rules="required" v-slot="{ errors }">
              <v-menu
                  ref="diEndDate"
                  v-model="diEndDate"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="profile.di_end_date"
                      label="DI Licence Expire Date"
                      prepend-inner-icon="mdi-calendar"
                      outlined
                      v-bind="attrs"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="profile.di_end_date"
                    no-title
                    scrollable
                >
                  <v-spacer></v-spacer>
                  <v-btn
                      text
                      color="primary"
                      @click="$refs.diEndDate.save(profile.di_end_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </ValidationProvider>
          </div>


          <div class="col-md-12 d-none">
            <v-text-field
                outlined
                label="Address *"
                placeholder="Address * "
                v-model="profile.address"
            ></v-text-field>
          </div>


          <div class="col-md-12">
            <v-textarea
                label="Biography *"
                placeholder="Biography * "
                v-model="profile.bio"
                auto-grow
            ></v-textarea>
          </div>
          <div class="col-md-12">
            <v-autocomplete
                v-model="profile.languages"
                :items="languages"
                multiple
                chips
                deletable-chips
                :search-input.sync="searchInput"
                @change="searchInput=''"
                item-text="name"
                item-value="code"
                label="Languages *"
                placeholder="Languages * "
                outlined
            ></v-autocomplete>
          </div>
        </div>
        <div class="d-flex">
          <v-btn
              v-if="profile.avatar.length > 0"
              color="primary"
              class="text-capitalize"
              @click="updateProfile();$emit('continue')"
          >
            Save Changes
          </v-btn>
          <v-btn
              v-else
              disabled
          >
            Save Changes
          </v-btn>
          <div class="ml-3" v-if="$store.state.user.user.kycVerification != 'Pending'">
            <UserDeleteDialog/>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>


<script>
import countryData from "../../../data/countryData";
import currencyData from "../../../data/currencyData";
import langData from "../../../data/langData";
import provinceData from "../../../data/provinceData";
import {maska} from "maska"
import VueGoogleAutocomplete from 'vue-google-autocomplete'
import UserDeleteDialog from "../../UserDeleteDialog";

export default {
  name: "EditProfileComponent",
  directives: {maska},
  components: {UserDeleteDialog, VueGoogleAutocomplete},
  props: {
    showDPDeleteBtn: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      countries: countryData,
      currencies: currencyData,
      languages: langData,
      provinces: provinceData,
      car: null,
      searchInput: '',
      profile: {
        first_name: "",
        last_name: "",
        email: "",
        phone_number: "",
        postal_code: "",
        city: "",
        street: "",
        unit_no: "",
        address: "",
        province: "Ontario",
        bio: "",
        languages: [],
        cover: "",
        avatar: "",
        fullName: "",
        evaluation: {},
      },
      licenceEndDate: false,
      diEndDate: false,
      birthDate: false
    }
  },
  created() {
    this.getProfile();
  },

  computed: {
    isEmptyProfile() {
      return Object.values(this.profile).every(property => property === '' || property.length === 0);
    }
  },


  methods: {
    async getProfile() {
      const url = "/v1/drivisa/instructors/me";
      const {data} = await axios.get(url);
      let profile = data.data;
      if (profile.cars.length > 0) {
        this.car = profile.cars[0]
      }
      this.profile.cover = profile.cover;
      this.profile.avatar = profile.avatar;
      this.profile.fullName = profile.fullName;
      this.profile.first_name = profile.firstName;
      this.profile.last_name = profile.lastName;
      this.profile.email = profile.email;
      this.profile.phone_number = profile.phoneNumber;
      this.profile.address = profile.address;
      this.profile.unit_no = profile.unit_no;
      this.profile.street = profile.street;
      this.profile.city = profile.city;
      this.profile.province = profile.province;
      this.profile.bio = profile.bio;
      this.profile.languages = profile.languages;
      this.profile.evaluation = profile.evaluation
      this.profile.postal_code = profile.postalCode;
      this.profile.licence_number = profile.licence_number;
      this.profile.licence_end_date = profile.licence_end_date;
      this.profile.di_number = profile.di_number;
      this.profile.di_end_date = profile.di_end_date;
      this.profile.birth_date = profile.birth_date;
    },
    getAddressData(addressData, placeResultData, id) {
      this.profile.address = placeResultData.formatted_address
      this.profile.unit_no = addressData.street_number;
      this.profile.street = addressData.route + ' , ' + placeResultData.vicinity;
      this.profile.city = addressData.locality;
      this.profile.postal_code = addressData.postal_code;
    },
    async updateProfile() {
      try {
        const url = "/v1/drivisa/instructors/update-profile";
        await axios.post(url, this.profile);
        await this.getProfile()
        this.$toasted.success("Profile Updated")
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Profile")
      }
    },
    async setProfilePhoto(zone) {
      try {
        const url = "/v1/user/update-userci-profile-picture";
        let file = this.$refs[zone].files[0];
        const formData = new FormData();
        formData.append('picture', file);
        formData.append('zone', zone);

        await axios.post(url, formData);
        await this.getProfile();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to upload Profile Photo")
      }
    },
    async removePhoto(zone) {
      try {
        const url = "/v1/user/delete-user-profile-picture";
        await axios.post(url, {zone: zone});
        await this.getProfile();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to remove photo")
      }
    }
  },

}
</script>

<style scoped>
.edit-profile-btn span {
  font-size: 10px !important;
}

.instructor-name {
  font-weight: 700;
  font-size: 20px;
  color: black;
  line-height: 1.4 !important;
}

.car {

}

.car span {
  line-height: 1.4 !important;
  letter-spacing: -.1px !important;
  margin-inline: 5px;
}

@media (max-width: 960px) {
  .v-card__text {
    margin-top: 4rem !important;
  }
}
</style>le>