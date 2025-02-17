<template>
  <div>
    <h3 class="text-h5 text-center py-3">Evaluations</h3>
    <div class="m-3">
      <table class="table table-bordered">
        <tr>
          <td colspan="2"><strong>Notes</strong></td>
        </tr>
        <tr>
          <td>Trainee Note</td>
          <td>{{ lesson.traineeNote }}</td>
        </tr>
        <tr>
          <td>Instructor Note</td>
          <td>{{ lesson.instructorNote }}</td>
        </tr>
        <tr v-for="(traineeEvaluation, i) in lesson.traineeEvaluation" :key="i">
          <td><strong>Trainee Evaluation</strong></td>
          <td>{{ traineeEvaluation }}</td>
        </tr>
        <tr>
          <td colspan="2">
            <strong>Instructor Evaluations</strong>
          </td>
        </tr>
        <tr>
          <td><strong>Title</strong></td>
          <td><strong>Value</strong></td>
        </tr>
        <tr
          v-for="(instructor_evaluation, a) in lesson.instructorEvaluations"
          :key="a"
        >
          <td>{{ instructor_evaluation.title }}</td>
          <td>{{ instructor_evaluation.value }}</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "EvaluationDetails",
  props: ["lesson"],
};
</script>