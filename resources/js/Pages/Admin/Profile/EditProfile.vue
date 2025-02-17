<template>
  <div>
    <MasterAdminLayout>
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
        <v-card-text v-if="profile != null">
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
              <h5 class="font-weight-bold mb-3 mt-3">{{ profile.fullName }}</h5>
            </div>
            <div>
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
          </div>
          <div class="row mt-5">
            <div class="col-md-6">
              <ValidationProvider
                name="first name"
                rules="alpha"
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
                rules="alpha"
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
              <ValidationProvider name="city" rules="alpha" v-slot="{ errors }">
                <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="City *"
                  placeholder="City * "
                  v-model="profile.city"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider
                name="province"
                rules="alpha"
                v-slot="{ errors }"
              >
                <v-text-field
                  outlined
                  :error-messages="errors[0]"
                  label="Province *"
                  placeholder="Province * "
                  v-model="profile.province"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-12">
              <v-text-field
                outlined
                label="Address *"
                placeholder="Address"
                v-model="profile.address"
              ></v-text-field>
            </div>

            <div class="col-md-12">
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
          </div>
          <v-btn
            color="primary btn-outline"
            class="text-capitalize"
            @click="updateProfile"
          >
            Save Changes
          </v-btn>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import { maska } from "maska";
export default {
  name: "EditProfile",
  components: { MasterAdminLayout },
  directives: { maska },
  data() {
    return {
      profile: {
        first_name: "",
        last_name: "",
        email: "",
        phone_number: "",
        postal_code: "",
        city: "",
        address: "",
        province: "",
        cover: "",
        avatar: "",
        fullName: "",
      },
    };
  },
  created() {
    this.getProfile();
  },
  methods: {
    async getProfile() {
      const { data } = await axios.get("/v1/drivisa/admin/get-profile-info");

      let admin = data.data;

      this.profile.cover = admin.cover;
      this.profile.avatar = admin.avatar;
      this.profile.fullName = admin.fullName;
      this.profile.first_name = admin.firstName;
      this.profile.last_name = admin.lastName;
      this.profile.email = admin.email;
      this.profile.phone_number = admin.phoneNumber;
      this.profile.address = admin.address;
      this.profile.city = admin.city;
      this.profile.province = admin.province;
      this.profile.postal_code = admin.postalCode;
      this.profile.bio = admin.bio;
      this.profile.languages = admin.languages;
    },
    async updateProfile() {
      try {
        const url = "/v1/drivisa/admin/update-profile";
        await axios.post(url, this.profile);
        await this.getProfile();
        this.$toasted.success("Profile Updated");
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to save Profile");
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
        this.$root.handleErrorToast(e, "Unable to delete profile photo");
      }
    },
  },
};
</script>
