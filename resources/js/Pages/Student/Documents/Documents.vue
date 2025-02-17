<template>
  <div>
    <MasterStudentLayout>
      <div>
        <DocumentComponent/>
      </div>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
import DocumentComponent from "../../../components/Student/Document/DocumentComponent";

export default {
  name: "Documents",
  components: {
    DocumentComponent,
    MasterStudentLayout,
  },

}
</script>

