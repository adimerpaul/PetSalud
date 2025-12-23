<template>
  <q-page class="q-pa-xs">
    <mascota-form />
  </q-page>
</template>
<script>

import {defineComponent} from "vue";
import MascotaForm from "pages/mascotas/MascotaForm.vue";

export default defineComponent({
  components: {MascotaForm}
})
</script>
