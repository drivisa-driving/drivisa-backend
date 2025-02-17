<template>
  <div>
    <MasterAdminLayout>
      <div class="page-header">
        <p class="page-heading">Discounts</p>
        <div class="admin_add_btn">
          <v-btn color="primary btn-outline" @click="dialog = true"
          >Add Discount
          </v-btn
          >
        </div>
      </div>
      <v-dialog v-model="dialog" max-width="500" persistent>
        <ValidationObserver v-slot="{ invalid }">
        <form @submit.prevent="createAdmin">

        <v-card>
          <h3 class="text-h5 text-center pt-3">
            {{ discount.id ? "Edit" : "Create A New" }} Discount
          </h3>
          <!-- Admin Create Form -->
          <v-card-text>
            <ValidationProvider
                name="Title"
                rules="required"
                v-slot="{ errors }"
            >
              <v-text-field
                  label="Title *"
                  placeholder="Title"
                  outlined
                  :error-messages="errors[0]"
                  v-model="discount.title"
                  v-on:keydown.enter="createAdmin"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                name="Code"
                rules="required"
                v-slot="{ errors }"
            >
              <v-text-field
                  label="Code *"
                  placeholder="Code"
                  outlined
                  :error-messages="errors[0]"
                  v-model="discount.code"
                  v-on:keydown.enter="createAdmin"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                name="Discount amount"
                rules="numeric|required"
                v-slot="{ errors }"
            >
              <v-text-field
                  label="Discount Amount *"
                  placeholder="Discount Amount "
                  outlined
                  :error-messages="errors[0]"
                  v-model="discount.discount_amount"
                  v-on:keydown.enter="createAdmin"
              ></v-text-field>
            </ValidationProvider>
              <ValidationProvider
                  v-slot="{ errors }"
                  rules="required"
                  name="type"
              >
                <v-autocomplete
                    :items="types"
                    outlined
                    dense
                    label="Select Type *"
                    item-text="value"
                    item-value="id"
                    height="54px"
                    :error-messages="errors[0]"
                    v-model="discount.type"
                >
                </v-autocomplete>
              </ValidationProvider>
                <ValidationProvider
                    v-slot="{ errors }"
                    name="type"
                    rules="required"
                >
                  <v-autocomplete
                      :items="packages"
                      outlined
                      dense
                      label="Select Lesson/Package Types *"
                      item-text="package_name_with_type"
                      item-value="id"
                      multiple
                      :error-messages="errors[0]"
                      v-model="discount.package_ids"
                  >
                  </v-autocomplete>
                </ValidationProvider>
              <ValidationProvider v-slot="{ errors }" name="Status"  rules="required">
                <v-autocomplete
                    :items="status"
                    outlined
                    dense
                    label="Select Status *"
                    item-text="value"
                    item-value="id"
                    :error-messages="errors[0]"
                    height="54px"
                    v-model="discount.status"
                >
                </v-autocomplete>
              </ValidationProvider>
              <ValidationProvider v-slot="{ errors }" name="start_at" rules="required">
                <label>Start At *:</label>
                <input type="date"
                       label="Start At "
                       class="form-control "
                       placeholder="Start At "
                       v-model="discount.start_at"
                       outlined
                       :error-messages="errors[0]"
                       :min="discount.id?discount.start_at:CurrentDate"
                       v-on:keydown.enter="createAdmin"
                />

              </ValidationProvider>
              <ValidationProvider v-slot="{ errors }" name="Expire At"  rules="required">
                <label class=" mt-5">Expire At *:</label>
                <input type="date"
                       class="form-control"
                       label="Expire At "
                       placeholder="Expire At "
                       v-model="discount.expire_at"
                       outlined
                       :error-messages="errors[0]"
                       :min="discount.id?discount.start_at:CurrentDate"
                       v-on:keydown.enter="createAdmin"
                />
              </ValidationProvider>

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
                type="submit" :disabled="invalid"
            >
              {{ discount.id ? "Update" : "Create" }}
            </v-btn>
          </div>
        </v-card>
        </form>
        </ValidationObserver>
      </v-dialog>

      <BasicDatatable :data="filteredData">

        <Column field="title" header="Title"></Column>
        <Column sortable field="code" header="Code"></Column>
        <Column sortable field="discount_amount_formatted" header="Discount Amount"></Column>
        <Column sortable field="type_format" header="Type"></Column>
        <Column sortable field="status_format" header="Status"></Column>
        <Column sortable field="start_at_formatted" header="Start At"></Column>
        <Column sortable field="expire_at_formatted" header="Expire At"></Column>

        <Column sortable header="Packages">
          <template #body="slotProps">
            <ol v-if="slotProps.data.packages.length">
           <li v-for="data in slotProps.data.packages.split(',')">
             {{data}}
           </li></ol>
          </template>
        </Column>
        <Column header="Actions">
          <template #body="slotProps">
            <v-menu left>
              <template v-slot:activator="{ on, attrs }">
              <span
                  class="mdi mdi-dots-vertical option-icon text-warning text-large float-left option-icon"
                  v-bind="attrs"
                  v-on="on"
              ></span>
              </template>
              <v-list>
                <v-list-item @click="discount=slotProps.data; dialog = true"> Edit</v-list-item>
                <v-list-item
                    @click="deleteDiscount(slotProps.data.id)"
                >
                  Delete
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </Column>
      </BasicDatatable>
    </MasterAdminLayout>
  </div>

</template>

<script>
import MasterAdminLayout from "../../../Pages/Admin/Layouts/MasterAdminLayout";

import SplitButton from "primevue/splitbutton";
import BasicDatatable from "../../../components/Global/BasicDatatable.vue";

export default {
  name: "List",
  components: {SplitButton, BasicDatatable, MasterAdminLayout},
  props: {
    showBtn: {
      type: Boolean,
      default: false,
    },
    onlyVerified: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      dialog: false,
      rejectDialog: false,
      requestData: {
        id: null,
        message: null,
      },
      types: [{
        'id': 'fixed',
        'value': 'Flat'
      },
        {
          'id': 'percent',
          'value': 'Percentage'
        }],
      status: [{
        'id': 'enable',
        'value': 'Enable'
      },
        {
          'id': 'disable',
          'value': 'Disable'
        }],
      discount: {
        id: 0,
        title: "",
        code: "",
        discount_amount: "",
        type: "",
        status: "",
        start_at: "",
        expire_at: "",
        package_ids:""
      },
      packages: [],
      documents: [],
      data: [],
      selectedOption: null,
      agreementDialog: false,
    };
  },
  mounted() {
    this.getDiscounts();
    this.getPackages();
  },
  computed: {
    filteredData() {
      return this.data;
    },
    CurrentDate()
    {
      const dateObj = new Date();
      const currentDate = new Date().toISOString().substr(0, 10)
      return currentDate;
    },

  },
  methods: {
    async getPackages() {
      try {
        const {data} = await axios.get("/v1/drivisa/admin/allPackages");
        this.packages = data.data;
      } catch (e) {
      }
    },    async getDiscounts() {
      try {
        const {data} = await axios.get("/v1/drivisa/admin/discounts");
        this.data = data.data;
      } catch (e) {
      }
    },
    async deleteDiscount(id) {
      try {
        const {data} = await axios.delete("/v1/drivisa/admin/discounts/" + id);
        this.data = data.data;
        this.$toasted.success(data.message);
        this.getDiscounts()
      } catch (e) {
      }
    },
    async createAdmin() {
      try {
        let url = "/v1/drivisa/admin/discounts/storeDiscount";
        let method = axios.put;

        const {data} = await method(url, this.discount);
        this.$toasted.success(data.message);
        this.clearForm();
        this.getDiscounts();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to save admin");
      }
    },

    clearForm() {
      this.discount = {
        id: 0,
        title: "",
        code: "",
        discount_amount: "",
        type: "",
        status: "",
        start_at: "",
        expire_at: "",
      };
      this.dialog = false;
    },
  },
};
</script>

<style scoped>
.option-icon {
  background-repeat: no-repeat;
  /*display: inline-block;*/
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
  float: right !important;
}
</style>
