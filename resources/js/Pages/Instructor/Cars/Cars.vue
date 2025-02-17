<template>
  <div>
    <MasterInstructorLayout>
      <CarsComponent/>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import CarsComponent from "../../../components/Instructor/Cars/CarsComponent";

export default {
  name: "Cars",
  components: {
    CarsComponent,
    MasterInstructorLayout,
  },
}
</script>

<style scoped>
.car h1 {
  font-family: Montserrat, sans-serif !important;
  font-weight: 400;
  font-size: 24px;
}

.option-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
}
</style>