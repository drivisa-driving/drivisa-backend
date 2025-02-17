<template>
  <div>
    <MasterInstructorLayout>
      <div>
        <DocumentComponent/>
      </div>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import DocumentComponent from "../../../components/Instructor/document/DocumentComponent";
import DocumentUploaderComponent from "../../../components/Instructor/document/DocumentUploaderComponent";

export default {
  name: "Documents",
  components: {DocumentUploaderComponent, DocumentComponent, MasterInstructorLayout},
}
</script>