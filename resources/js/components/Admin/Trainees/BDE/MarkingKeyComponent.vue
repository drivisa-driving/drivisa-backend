<template>
  <div>
    <p style="page-break-after: always"></p>
    <div style="width: 100%">
      <img
        src="/assets/media/logos/drivisa-logo200_80.svg"
        style="float: left; padding: 20px; padding-bottom: 0px; height: 60px"
        class="d-none"
      />
    </div>
    <div style="width: 100%; margin-bottom: 10px; padding-top: 20px">
      <div>
        <p style="text-align: center"><strong>Marking Key:</strong></p>
        <div style="width: 100%; padding-top: 0px; text-align: center">
          <p>
            <strong><u>E</u></strong> Exceeds Lesson Objective &emsp;&emsp;
            <strong><u>M</u></strong> Meets Lesson Objective &emsp;&emsp;
            <strong><u>P</u></strong> Progressing well &emsp;&emsp;
            <strong><u>N</u></strong> Needs More Practice
          </p>
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-left: 1px solid black;
          border-top: 1px solid black;
          text-align: center;
        "
      >
        <div style="width: 95%; border-right: 1px solid black">
          <strong>Lessons:</strong>
        </div>
        <div
          style="width: 5%; border-right: 1px solid black"
          :key="k"
          v-for="(marking_log, k) in bdeLog.marking_log_list"
        >
          <strong>{{ k + 1 }}</strong>
        </div>
      </div>
      <div
        style="
          display: flex;
          width: 100%;
          border-left: 1px solid black;
          border-top: 1px solid black;
          text-align: center;
        "
        :key="i"
        v-for="(marking_key, i) in bdeLog.marking_keys"
      >
        <div style="width: 95%; border-right: 1px solid black">
          {{ marking_key }}
        </div>
        <div
          style="width: 5%; border-right: 1px solid black"
          :key="j"
          v-for="(marking_log, j) in bdeLog.marking_log_list"
        >
          {{
            marking_log[marking_key] !== undefined
              ? marking_log[marking_key]["value"]
              : "-"
          }}
        </div>
      </div>
      <div style="border-bottom: 1px solid black; width: 100%"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MarkingKeyComponent",
  props: ["bdeLog"],
};
</script>
<style scoped>

</style>