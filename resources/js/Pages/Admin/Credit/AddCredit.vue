<template>
  <div>
    <div class="row mt-5">
      <div class="col-md-6">
        <ValidationProvider name="Trainee" v-slot="{ errors }">
          <v-autocomplete
            :items="trainees"
            outlined
            dense
            label="Select Trainee *"
            item-text="full_name"
            item-value="id"
            height="54px"
            v-model="requestData.trainee_id"
          >
          </v-autocomplete>
        </ValidationProvider>
      </div>

      <div class="col-md-6">
        <ValidationProvider name="Package" v-slot="{ errors }">
          <v-autocomplete
            :items="packages"
            outlined
            dense
            label="Select Package *"
            item-text="package_name_with_type"
            item-value="id"
            height="54px"
            @change="setCreditHours"
            v-model="requestData.package_id"
          ></v-autocomplete>
        </ValidationProvider>
      </div>

      <div class="col-md-6">
        <ValidationProvider name="credit" rules="number" v-slot="{ errors }">
          <v-text-field
            outlined
            label="Credit Hours *"
            placeholder="Enter Credit Hours * "
            v-model="requestData.credit"
          >
          </v-text-field>
        </ValidationProvider>
      </div>

      <div class="col-md-6">
        <ValidationProvider name="" rules="number" v-slot="{ errors }">
          <v-text-field
            outlined
            label="Payment Intent ID *"
            placeholder="Enter Payment Intent ID* "
            v-model="requestData.payment_intent_id"
          >
          </v-text-field>
        </ValidationProvider>
      </div>
      <div class="col-md-6">
        <v-menu
          v-model="menu2"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="requestData.payment_date"
              label="Payment Date *"
              prepend-inner-icon="mdi-calendar"
              readonly
              outlined
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="requestData.payment_date"
            @input="menu2 = false"
          ></v-date-picker>
        </v-menu>
      </div>
    </div>
    <v-btn
      color="primary btn-outline"
      class="text-capitalize"
      @click="savePackage"
    >
      Save
    </v-btn>
  </div>
</template>

<script>
export default {
  name: "AddCredit",
  props: ["trainees"],
  data() {
    return {
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      menu2: false,
      packages: [],
      requestData: {
        trainee_id: null,
        package_id: null,
        credit: null,
        payment_intent_id: null,
        payment_date: null,
      },
    };
  },
  mounted() {
    this.getPackages();
  },
  methods: {
    setCreditHours($event) {
      let selectedPackage = this.packages.find((pk) => pk.id == $event);
      this.requestData.credit = selectedPackage.packageData.hours;
    },
    async getPackages() {
      try {
        const url = "/v1/drivisa/admin/packages/get-selected-packages";
        const { data } = await axios.get(url);
        this.packages = data.data;
      } catch (e) {}
    },
    async savePackage() {
      try {
        const url = "/v1/drivisa/admin/trainees/credit/add";
        const { data } = await axios.post(url, this.requestData);
        this.$toasted.success("Course Credit Added");
        this.requestData = {
          trainee_id: null,
          package_id: null,
          credit: null,
          payment_intent_id: null,
          payment_date: null,
        };
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable To Add Credit");
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>