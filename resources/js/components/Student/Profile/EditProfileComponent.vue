<template>
  <div>
    <div class="d-none">
      <input
        type="file"
        @change="setProfilePhoto('profile_picture')"
        ref="profile_picture"
        id="profile_picture"
      />
      <input
        type="file"
        @change="setProfilePhoto('cover_picture')"
        ref="cover_picture"
        id="cover_picture"
      />
    </div>
    <v-card elevation="0" class="profile_section">
      <v-card-text>
        <div
          class="cover-section"
          :style="{ backgroundImage: 'url(' + profile.cover + ')' }"
        >
          <div class="flex-box-start"></div>
          <div class="instructor-avatar">
            <div class="position-relative">
              <img
                class="avatar"
                :src="profile.avatar"
                onclick="document.querySelector('#profile_picture').click()"
              />
              <span
                v-if="showDPDeleteBtn"
                class="position-absolute mdi mdi-delete text-danger"
                style="
                  bottom: 0;
                  font-size: 22px;
                  border-radius: 50%;
                  color: white;
                  background: #0f1317;
                  padding: 7px;
                  cursor: pointer;
                  right: 0;
                "
                @click="removePhoto('profile_picture')"
              ></span>
            </div>
            <h5 class="font-weight-bold mb-3 mt-3 text-dark">
              {{ profile.fullName }}
            </h5>
          </div>
          <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
              <span class="edit-profile-btn" v-bind="attrs" v-on="on">
                <i class="mdi mdi-camera"></i>
                <span class="d-none d-sm-block">Upload Cover</span>
              </span>
            </template>
            <v-list>
              <v-list-item
                onclick="document.querySelector('#cover_picture').click()"
              >
                <v-list-item-title>Change Photo</v-list-item-title>
              </v-list-item>
              <v-list-item @click="removePhoto('cover_picture')">
                <v-list-item-title>Remove Photo</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>
        <div class="row mt-5">
          <div class="col-md-6">
            <ValidationProvider
              name="first name"
              rules="required"
              v-slot="{ errors }"
            >
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
            <ValidationProvider
              name="last name"
              rules="required"
              v-slot="{ errors }"
            >
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
            <ValidationProvider
              name="email address"
              rules="email"
              v-slot="{ errors }"
            >
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
            <ValidationProvider
              name="street"
              rules="required"
              v-slot="{ errors }"
            >
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
            <ValidationProvider
              name="city"
              rules="required"
              v-slot="{ errors }"
            >
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
            <v-select
              :items="provinces"
              item-text="name"
              item-value="name"
              outlined
              label="Province *"
              placeholder="Province * "
              v-model="profile.province"
            ></v-select>
          </div>
          <div class="col-md-6">
            <ValidationProvider
              name="postal code"
              rules="required"
              v-slot="{ errors }"
            >
              <v-text-field
                outlined
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

          <div class="col-md-12 d-none">
            <v-text-field
              outlined
              label="Address *"
              placeholder="Address * "
              v-model="profile.address"
            ></v-text-field>
          </div>
        </div>
        <div class="d-flex">
          <v-btn
            color="primary"
            class="text-capitalize"
            @click="
              updateProfile();
              $emit('studentContinue');
            "
          >
            Save Changes
          </v-btn>
          <div
            class="ml-3"
            v-if="$store.state.user.user.kycVerification != 'Pending'"
          >
            <UserDeleteDialog />
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>


<script>
import provinceData from "../../../data/provinceData";
import VueGoogleAutocomplete from "vue-google-autocomplete";
import { maska } from "maska";
import UserDeleteDialog from "../../UserDeleteDialog";

export default {
  name: "EditProfileComponent",
  components: { UserDeleteDialog, VueGoogleAutocomplete },
  directives: { maska },
  props: {
    showDPDeleteBtn: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      provinces: provinceData,
      profile: {
        first_name: "",
        last_name: "",
        email: "",
        phone_number: "",
        postal_code: "",
        city: "",
        address: "",
        province: "",
        street: "",
        unit_no: "",
        cover: "",
        avatar: "",
        fullName: ""
      },
      birthDate: false
    };
  },
  created() {
    this.getProfile();
  },
  methods: {
    async getProfile() {
      const url = "/v1/drivisa/trainees/me";
      const { data } = await axios.get(url);
      let profile = data.data;

      this.profile.cover = profile.cover;
      this.profile.avatar = profile.avatar;
      this.profile.fullName = profile.fullName;
      this.profile.first_name = profile.firstName;
      this.profile.last_name = profile.lastName;
      this.profile.email = profile.email;
      this.profile.phone_number = profile.phoneNumber;
      this.profile.address = profile.address;
      this.profile.street = profile.street;
      this.profile.unit_no = profile.unit_no;
      this.profile.city = profile.city;
      this.profile.province = profile.province;
      this.profile.postal_code = profile.postalCode;
      this.profile.bio = profile.bio;
      this.profile.languages = profile.languages;
      this.profile.birth_date = profile.birthDate;
    },
    getAddressData(addressData, placeResultData, id) {
      this.profile.address = placeResultData.formatted_address;
      this.profile.unit_no = addressData.street_number;
      this.profile.street =
        addressData.route + " , " + placeResultData.vicinity;
      this.profile.city = addressData.locality;
      this.profile.postal_code = addressData.postal_code;
    },
    async updateProfile() {
      try {
        const url = "/v1/drivisa/trainees/update-profile";
        await axios.post(url, this.profile);
        await this.getProfile();
        this.$toasted.success("Profile Updated");
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to update Profile");
      }
    },
    async setProfilePhoto(zone) {
      try {
        const url = "/v1/user/update-user-profile-picture";
        let file = this.$refs[zone].files[0];
        const formData = new FormData();
        formData.append("picture", file);
        formData.append("zone", zone);

        await axios.post(url, formData);
        await this.getProfile();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to upload profile photo");
      }
    },
    async removePhoto(zone) {
      try {
        const url = "/v1/user/delete-user-profile-picture";
        await axios.post(url, { zone: zone });
        await this.getProfile();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete photo");
      }
    },
  },
};
</script>
