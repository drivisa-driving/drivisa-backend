<template>
  <div>
    <div>
      <img
        src="/assets/media/logos/drivisa-logo200_80.svg"
        style="float: left; padding: 20px; padding-bottom: 0px; height: 60px"
      />
    </div>
    <div style="display: flex; width: 100%">
      <div style="width: 100%; text-align: center">
        <h2>STUDENT IN-CAR RECORD</h2>
      </div>
    </div>

    <div style="border: 1px solid black; margin-bottom: 20px">
      <div style="display: flex; width: 100%; font-size: 14px">
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          STUDENT'S NAME: {{ bdeLog.trainee?.name }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          LICENCE NUMBER:
          <input type="text" style="border: none; outline: none" />
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-top: 1px solid black;
          font-size: 14px;
        "
      >
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          ADDRESS: {{ bdeLog.trainee?.address }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          DATE OF ISSUE:
          <input type="date" style="border: none; outline: none" />
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-top: 1px solid black;
          font-size: 14px;
        "
      >
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          CITY: {{ bdeLog.trainee?.city }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          DATE OF EXPIRY:
          <input type="date" style="border: none; outline: none" />
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-top: 1px solid black;
          font-size: 14px;
        "
      >
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          POSTAL CODE: {{ bdeLog.trainee?.postal_code }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          ROAD TEST DATE:
          <input type="date" style="border: none; outline: none" />
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-top: 1px solid black;
          font-size: 14px;
        "
      >
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          CELL_PHONE: {{ bdeLog.trainee?.phone_number }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          COURSE DATE:
          <input type="date" style="border: none" />
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-top: 1px solid black;
          font-size: 14px;
        "
        v-for="instructor in bdeLog.instructors"
        :key="instructor.di_number"
      >
        <div
          style="width: 100%; border-right: 1px solid black; padding-left: 10px"
        >
          INSTRUCTOR'S NAME: {{ instructor.full_name }}
        </div>
        <div style="width: 100%; padding-left: 10px">
          D.I N.O: {{ instructor.di_number }}
        </div>
      </div>
    </div>

    <div style="border: 1px solid black; border-bottom: 0">
      <div
        style="
          display: flex;
          width: 100%;
          border-bottom: 1px solid black;
          text-align: center;
        "
      >
        <div style="width: 10%; border-right: 1px solid black">#</div>
        <div style="width: 80%; border-right: 1px solid black">
          Date<br />MM/DD/YYYY
        </div>
        <div style="width: 30%; border-right: 1px solid black">
          Time<br />In
        </div>
        <div style="width: 30%; border-right: 1px solid black">
          Time<br />Out
        </div>
        <div style="width: 40%; border-right: 1px solid black">
          Instructor<br />Signature
        </div>
        <div style="width: 40%; border-right: 1px solid black">
          Students<br />Signature
        </div>
        <div style="width: 100%">Notes</div>
      </div>
      <div v-if="bdeLog.bde_log_list?.length > 0">
        <div
          style="
            display: flex;
            width: 100%;
            border-bottom: 1px solid black;
            text-align: center;
          "
          :key="i"
          v-for="(bde_log, i) in bdeLog.bde_log_list"
        >
          <div
            style="width: 10%; border-right: 1px solid black; padding-top: 10px"
          >
            {{ i + 1 }}
          </div>
          <div
            style="width: 80%; border-right: 1px solid black; padding-top: 5px"
          >
            {{ bde_log.date }}
          </div>
          <div
            style="width: 30%; border-right: 1px solid black; padding-top: 5px"
          >
            {{ bde_log.time_in }}
          </div>
          <div
            style="width: 30%; border-right: 1px solid black; padding-top: 5px"
          >
            {{ bde_log.time_out }}
          </div>
          <div
            style="width: 40%; border-right: 1px solid black; padding-top: 5px"
          >
            <img :src="bde_log.instructor_sign" alt="" width="40px" />
          </div>
          <div
            style="width: 40%; border-right: 1px solid black; padding-top: 5px"
          >
            <img :src="bde_log.trainee_sign" alt="" width="40px" />
          </div>
          <div style="width: 100%; padding-top: 5px">
            {{ bde_log.notes }}
          </div>
        </div>
      </div>
      <div v-else style="border-bottom: 1px solid black; padding: 10px">
        <strong>No Record Found</strong>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["bdeLog"],
};
</script>
<style scoped lang="scss">
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-calendar-picker-indicator {
  display: none;
  -webkit-appearance: none;
}

@media print {
  .header,
  .footer-bg,
  .v-btn__content i.v-icon,
  .v-btn__content .v-icon,
  .print-form-btn,
  .action-area,
  .sign-container,
  .update-button-div,
  input,
  v-btn,
  img.img-fluid{
    display: none !important;
  }

  a[href]:after {
    content: none !important;
  }

  @page {
    margin-top: 10mm;
    margin-bottom: 10mm;
  }
  body {
    padding-top: 72px;
    padding-bottom: 72px;
  }
}
</style>