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
        <ValidationProvider name="Bonus Credit" v-slot="{ errors }">
          <v-autocomplete
            :items="bonus_type"
            outlined
            dense
            label="Select Bonus Credit Type *"
            item-text="type"
            item-value="value"
            height="54px"
            v-model="requestData.type"
          ></v-autocomplete>
        </ValidationProvider>
      </div>

      <div class="col-md-6">
        <ValidationProvider name="Credit Hour's" v-slot="{ errors }">
          <v-text-field
            type="number"
            outlined
            label="Credit Hour's *"
            placeholder="Enter Credit Hour's * "
            v-model="requestData.credit"
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
              label="Date *"
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
      @click="addBonusCredit"
    >
      Save
    </v-btn>
  </div>
</template>

<script>
export default {
  name: "Credit",
  props: ["trainees"],
  data() {
    return {
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      menu2: false,
      bonus_type: [
        { type: "In Car Private Lesson", value: "Bonus" },
        { type: "BDE", value: "Bonus_BDE" },
      ],
      requestData: {
        trainee_id: null,
        type: null,
        credit: null,
        payment_date: null,
      },
    };
  },
  methods: {
    async addBonusCredit() {
      try {
        const url = "/v1/drivisa/admin/trainees/credit/bonus";
        const { data } = await axios.post(url, this.requestData);
        this.$toasted.success(data.message);
        this.requestData = {
          trainee_id: null,
          type: null,
          credit: null,
          payment_date: null,
        };
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable To Add Bonus Credit");
      }
    },
  },
};
</script>
<style scoped></style>
