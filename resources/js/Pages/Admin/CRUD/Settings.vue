<template>
  <div>
    <MasterAdminLayout>
      <v-dialog v-model="dialog" persistent max-width="290"> </v-dialog>
      <v-card elevation="0" class="profile_section">
        <v-card-text>
          <div class="page-header pl-0">
            <p class="page-heading">Core Settings</p>
          </div>
          <hr />

          <div class="d-lg-flex justify-content-lg-between">
            <v-dialog v-model="add_setting_dialog" persistent max-width="600px">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  small
                  v-bind="attrs"
                  v-on="on"
                  class="text-capitalize d-none"
                >
                  Add Setting
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="text-h5">Add Setting</span>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12">
                        <ValidationProvider
                          name="name"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Name"
                            outlined
                            :error-messages="errors[0]"
                            v-model="setting.name"
                            required
                          ></v-text-field>
                        </ValidationProvider>
                        <ValidationProvider
                          name="value"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Value"
                            outlined
                            :error-messages="errors[0]"
                            v-model="setting.value"
                            required
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    class="text-white text-capitalize"
                    color="red"
                    @click="add_setting_dialog = false"
                  >
                    Close
                  </v-btn>
                  <v-btn
                    class="text-white text-capitalize"
                    color="green"
                    @click="saveSetting"
                  >
                    Create
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </div>
          <div class="my-4">
            <div
              class="setting-box"
              v-for="setting in settings"
              :key="setting.id"
            >
              <div class="row align-items-center">
                <div class="col-md-3 text-left text-lg-left">
                  <p>
                    {{
                      setting.name.replaceAll("_", " ") | setting | uppercase
                    }}
                  </p>
                </div>
                <div class="col-md-7 mt-0 mt-md-5">
                  <ValidationProvider
                    name="value"
                    rules="required"
                    v-slot="{ errors }"
                  >
                    <v-text-field
                      v-model="setting.value"
                      outlined
                      :error-messages="errors[0]"
                      label="Value"
                      :disabled="setting.is_disabled"
                      placeholder="Value"
                    >
                    </v-text-field>
                  </ValidationProvider>
                </div>
                <div class="col-md-2 text-center">
                  <v-btn
                    color="warning"
                    small
                    class="mr-3"
                    v-if="setting.is_disabled"
                    @click="onSettingEdit(setting.id)"
                    >Edit
                  </v-btn>
                  <v-btn
                    color="success"
                    class="mr-3 text-white text-capitalize"
                    v-else
                    small
                    @click="onSettingUpdate(setting.id)"
                    >Update
                  </v-btn>
                </div>
              </div>
              <hr class="d-block d-sm-none" />
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";

export default {
  name: "Settings",
  components: { MasterAdminLayout },

  data() {
    return {
      add_setting_dialog: false,
      dialog: false,
      delete_id: null,
      setting: {
        name: "",
        value: "",
      },
      settings: [],
    };
  },

  mounted() {
    this.getSetting();
  },

  methods: {
    async saveSetting() {
      try {
        await axios.post("/v1/admin/settings", this.setting);
        await this.getSetting();
        this.clearForm();
        this.add_setting_dialog = false;
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Settings Details");
      }
    },
    clearForm() {
      this.setting = {
        name: "",
        value: "",
      };
    },

    async getSetting() {
      const { data } = await axios.get("/v1/admin/settings");
      this.settings = [];
      data.data.forEach((item) => {
        this.settings.push({
          id: item.id,
          name: item.name,
          value: item.value,
          is_disabled: true,
        });
      });
    },

    async onSettingEdit(id) {
      this.settings.find((item) => item.id === id).is_disabled = false;
    },

    async onSettingUpdate(id) {
      try {
        let setting = this.settings.find((item) => item.id === id);
        const url = "/v1/admin/settings/" + id;
        await axios.put(url, { value: setting.value });
        this.settings.find((item) => item.id === id).is_disabled = true;
      } catch (e) {}
    },

    async deleteSetting(id) {
      try {
        await axios.delete("/v1/admin/settings/" + this.delete_id);
        this.dialog = false;
        await this.getSetting();
        this.$toasted.success("Setting Deleted");
      } catch (e) {
        this.$toasted.error("Unable to Delete Setting");
      }
    },
  },
};
</script>