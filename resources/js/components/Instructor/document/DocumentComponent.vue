<template>
  <v-card elevation="0" class="border">
    <v-card-text class="row">
      <!--  Driver licence    -->
      <DocumentUploaderComponent
          :document="getZoneObject('driver_licence')"
          zone_name="driver_licence"
          documentName="Driver's Licence"
      />

      <!--  Vehicle insurance policy  (Liability Slip)  -->
      <DocumentUploaderComponent
          :document="getZoneObject('vehicle_insurance_policy_liability_slip')"
          zone_name="vehicle_insurance_policy_liability_slip"
          documentName="Vehicle Insurance Policy (Liability Slip) "
      />

      <!--  Vehicle insurance policy  (OPCF 6D)  -->
      <DocumentUploaderComponent
          :document="getZoneObject('vehicle_insurance_policy_opcf_6d')"
          zone_name="vehicle_insurance_policy_opcf_6d"
          documentName="Vehicle Insurance Policy (OPCF 6D)"
      />

      <!--  Vehicle Registration    -->
      <DocumentUploaderComponent
          :document="getZoneObject('vehicle_registration')"
          zone_name="vehicle_registration"
          documentName="Vehicle Registration "
      />

      <!--  Safety for vehicle    -->
      <DocumentUploaderComponent
          :document="getZoneObject('safety_vehicle')"
          zone_name="safety_vehicle"
          documentName="Safety For Vehicle"
      />

      <!--  Driving instructor license   -->
      <DocumentUploaderComponent
          :document="getZoneObject('driving_instructor_license')"
          zone_name="driving_instructor_license"
          documentName="Driving Instructor License"
      />

      <!--  Dual brake and license plate   -->
      <DocumentUploaderComponent
          :document="getZoneObject('dual_brake')"
          zone_name="dual_brake"
          documentName="Dual Brake Photo"
      />

      <!--  Dual brake and license plate   -->
      <DocumentUploaderComponent
          :document="getZoneObject('front_picture_of_car_showing_the_plate')"
          zone_name="front_picture_of_car_showing_the_plate"
          documentName="Front picture Of Car Showing The Plate"
      />

      <!--  college Certificate   -->
      <DocumentUploaderComponent
          :document="getZoneObject('college_certificate')"
          zone_name="college_certificate"
          documentName="College Certificate"
      />
    </v-card-text>
  </v-card>
</template>

<script>
import DocumentStatus from "../../../components/Instructor/document/DocumentStatus";
import DocumentUploaderComponent from "./DocumentUploaderComponent";

export default {
  name: "DocumentComponent",
  components: {DocumentUploaderComponent, DocumentStatus},
  data() {
    return {
      documents: []
    }
  },
  mounted() {
    this.getDocuments()
  },
  methods: {
    async getDocuments() {
      const url = "/v1/drivisa/instructors/documents";
      const {data} = await axios.get(url);
      this.documents = data.data;
    },
    getZoneObject(zone) {
      return this.documents.find(doc => doc.zone === zone);
    },
  }
}
</script>

<style scoped>
.document-box {
  margin-bottom: 20px;
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  place-content: stretch center;
  align-items: stretch;
  flex: 1 1 33%;
}

.document-box h3 {
  font: 400 24px/32px Montserrat, sans-serif !important;
  letter-spacing: normal;
  margin: 0 0 16px;
  color: rgba(0, 0, 0, .87);
}

.upload-box {
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  place-content: center;
  align-items: center;
  background-color: #eee;
  min-width: 309px;
  max-width: 360px;
  height: 180px;
  cursor: pointer;
  border: 1px dashed silver;
  border-radius: 2px;
}

.upload-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  color: #008a2e;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
}
</style>