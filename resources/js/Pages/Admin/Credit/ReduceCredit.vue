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
        <ValidationProvider name="course" v-slot="{ errors }">
          <v-autocomplete
            :items="courses"
            outlined
            dense
            label="Select Course *"
            item-text="text"
            item-value="text"
            height="54px"
            v-model="requestData.course_type"
          ></v-autocomplete>
        </ValidationProvider>
      </div>

      <div class="col-md-6">
        <ValidationProvider name="credit" v-slot="{ errors }">
          <v-text-field
            type="number"
            outlined
            label="Credit Hours To Reduce *"
            placeholder="enter credit hours to reduce * "
            v-model="requestData.credit_reduce"
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
              v-model="requestData.date"
              label="Date *"
              prepend-inner-icon="mdi-calendar"
              readonly
              outlined
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="requestData.date"
            @input="menu2 = false"
          ></v-date-picker>
        </v-menu>
      </div>
      <div class="col-md-12">
        <v-textarea
          outlined
          auto-grow
          label="Specify Reason For Reducing Credit Hour's *"
          placeholder="reason"
          v-model="requestData.note"
        ></v-textarea>
      </div>
    </div>
    <v-btn
      color="primary btn-outline"
      class="text-capitalize"
      @click="reduceCreditHour"
    >
      Save
    </v-btn>
  </div>
</template>

<script>
export default {
  name: "ReduceCredit",
  props: ["trainees"],
  data() {
    return {
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      menu: false,
      modal: false,
      menu2: false,
      requestData: {
        trainee_id: null,
        course_type: null,
        credit_reduce: null,
        note: null,
        date: null,
      },
      courses: [{ text: "BDE" }, { text: "Package" }],
    };
  },
  methods: {
    async reduceCreditHour() {
      try {
        const url = "/v1/drivisa/admin/trainees/credit/reduce";
        const { data } = await axios.post(url, this.requestData);
        this.$toasted.success(data.message);
        this.requestData = {
          trainee_id: null,
          course_type: null,
          credit_reduce: null,
          note: null,
          date: null,
        };
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable To Reduce Credit");
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>