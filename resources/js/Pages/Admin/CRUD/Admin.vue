<template>
  <div>
    <MasterAdminLayout>
      <v-card>
        <v-card-text>
          <div class="page-header pr-0">
            <p class="page-heading">Admins</p>
            <div class="admin_add_btn">
              <v-btn color="primary btn-outline" @click="dialog = true"
                >Add Admin</v-btn
              >
            </div>
          </div>

          <!-- Delete Admin Dialog -->

          <v-dialog v-model="deleteDialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">Do you want to delete?</h3>
              <!-- Admin Create Form -->
              <v-card-text class="text-center">
                <p>Once Deleted Admin Can't be recovered back</p>
              </v-card-text>

              <div class="d-flex justify-content-around px-2 py-3">
                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  text
                  @click="
                    deleteDialog = false;
                    delete_id = null;
                  "
                >
                  Cancel
                </v-btn>

                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  @click="deleteAdmin"
                >
                  Confirm
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Create Admin Dialog -->

          <v-dialog v-model="dialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                {{ admin.id ? "Edit" : "Create A New" }} Admin
              </h3>
              <!-- Admin Create Form -->
              <v-card-text>
                <ValidationProvider
                  name="first name"
                  rules="alpha"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="First Name *"
                    placeholder="First Name"
                    outlined
                    :error-messages="errors[0]"
                    v-model="admin.first_name"
                    v-on:keydown.enter="createAdmin"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider
                  name="last name"
                  rules="alpha"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Last Name *"
                    placeholder="Last Name"
                    outlined
                    :error-messages="errors[0]"
                    v-model="admin.last_name"
                    v-on:keydown.enter="createAdmin"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider
                  name="email address"
                  rules="email"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Email Address *"
                    placeholder="Email Address "
                    outlined
                    :error-messages="errors[0]"
                    v-model="admin.email"
                    v-on:keydown.enter="createAdmin"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationObserver>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="password"
                    rules="confirmed:confirmation"
                  >
                    <v-text-field
                      label="Password *"
                      placeholder="Password "
                      v-model="admin.password"
                      :type="type"
                      outlined
                      :error-messages="errors[0]"
                      :append-icon="icon"
                      @click:append="changePasswordType"
                      v-on:keydown.enter="createAdmin"
                    ></v-text-field>
                  </ValidationProvider>
                  <ValidationProvider v-slot="{ errors }" vid="confirmation">
                    <v-text-field
                      label="Confirm Password *"
                      placeholder="ConfirmPassword "
                      v-model="admin.password_confirmation"
                      :type="type"
                      outlined
                      :error-messages="errors[0]"
                      :append-icon="icon"
                      v-on:keydown.enter="createAdmin"
                      @click:append="changePasswordType"
                    ></v-text-field>
                  </ValidationProvider>
                </ValidationObserver>
              </v-card-text>

              <div class="d-flex justify-content-around px-2 py-3">
                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  @click="clearForm"
                >
                  Close
                </v-btn>

                <v-btn
                  class="text-white text-capitalize"
                  color="green"
                  @click="createAdmin"
                >
                  {{ admin.id ? "Update" : "Create" }}
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Admin Datatable -->
          <AdminDatatablePrimeVue
            ref="table"
            @edit="editAdmin"
            @delete="dialogMethod"
          />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import AdminDatatablePrimeVue from "../../../components/Admin/Crud/AdminDatatablePrimeVue";

export default {
  name: "Admin",
  components: { MasterAdminLayout, AdminDatatablePrimeVue },
  data() {
    return {
      dialog: false,
      deleteDialog: false,
      delete_id: null,
      type: "password",
      icon: "mdi-eye-off",
      admin: {
        id: null,
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
    };
  },
  methods: {
    dialogMethod(id) {
      this.deleteDialog = true;
      this.delete_id = id;
    },
    editAdmin(admin) {
      this.admin = {
        id: admin.id,
        first_name: admin.first_name,
        last_name: admin.last_name,
        email: admin.email,
        password: "",
        password_confirmation: "",
      };
      this.dialog = true;
    },
    updateTable() {
      this.$refs.table.getAllAdmin();
    },
    changePasswordType() {
      if (this.type === "password") {
        this.type = "text";
        this.icon = "mdi-eye";
      } else {
        this.type = "password";
        this.icon = "mdi-eye-off";
      }
    },
    clearForm() {
      this.admin = {
        id: null,
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        password_confirmation: "",
      };
      this.dialog = false;
    },
    async createAdmin() {
      try {
        let url = "/v1/drivisa/admin/create-admin";
        let method = axios.post;

        if (this.admin.id) {
          url = "/v1/drivisa/admin/update-admin";
          method = axios.put;
        }

        const { data } = await method(url, this.admin);
        this.$toasted.success(data.message);
        this.updateTable();
        this.clearForm();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to save admin");
      }
    },
    async deleteAdmin() {
      try {
        let url = `/v1/drivisa/admin/${this.delete_id}/delete`;
        let method = axios.delete;

        const { data } = await method(url);
        this.$toasted.success(data.message);
        this.updateTable();
        this.deleteDialog = false;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete Admin");
      }
    },
  },
};
</script>

<style>
.admin_add_btn {
  text-align: right;
}

@media (max-width: 960px) {
  .admin_add_btn {
    text-align: left !important;
  }
}

.v-data-footer {
  padding: 0 !important;
}
</style>
