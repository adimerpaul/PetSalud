<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
      <q-card-section>
        <div class="row">
          <div class="col-12 col-md-2 q-pa-xs">
            <q-btn @click="$router.push({name: 'mascotas'})" label="Atrás" color="primary" icon="arrow_back" no-caps
                   size="10px" :loading="loading"/>
          </div>
          <div class="col-6 col-md-2" style="line-height: 0.8">
            <div class="text-caption text-bold">
              Nombre:
            </div>
            {{ mascota.nombre }}
          </div>
          <div class="col-6 col-md-2" style="line-height: 0.8">
            <div class="text-caption text-bold">
              Especie:
            </div>
            {{ mascota.especie }}
          </div>
          <div class="col-6 col-md-2" style="line-height: 0.8">
            <div class="text-caption text-bold">
              Raza:
            </div>
            {{ mascota.raza }}
          </div>
          <div class="col-6 col-md-2" style="line-height: 0.8">
            <div class="text-caption text-bold">
              Sexo:
            </div>
            {{ mascota.sexo }}
          </div>
        </div>
        <q-card flat bordered>
          <q-tabs
            v-model="tab"
            dense
            align="justify"
            narrow-indicator
          >
            <q-tab name="mascota" :class="{'bg-primary text-white': tab === 'mascota'}">
              <q-btn :loading="loading" flat dense no-caps icon="pets" label="Mascota"
                     :color="tab === 'mascota' ? 'white' : 'black'"/>
            </q-tab>
            <!--            tab historial-->
            <q-tab name="historial" :class="{'bg-blue text-white': tab === 'historial'}">
              <q-btn :loading="loading" flat dense no-caps icon="history" label="Historial clinico"
                     :color="tab === 'historial' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="ventas" :class="{'bg-indigo text-white': tab === 'ventas'}">
              <q-btn :loading="loading" flat dense no-caps icon="shopping_cart" label="Ventas"
                     :color="tab === 'ventas' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="complementos" :class="{'bg-green text-white': tab === 'complementos'}">
              <q-btn :loading="loading" flat dense no-caps icon="local_hospital" label="Complementos"
                     :color="tab === 'complementos' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="certificados" :class="{'bg-red text-white': tab === 'certificados'}">
              <q-btn :loading="loading" flat dense no-caps icon="receipt" label="Certificados"
                     :color="tab === 'certificados' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="laboratorios" :class="{'bg-blue text-white': tab === 'laboratorios'}">
              <q-btn :loading="loading" flat dense no-caps icon="local_hospital" label="Laboratorios"
                     :color="tab === 'laboratorios' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="vacunas" :class="{'bg-black text-white': tab === 'vacunas'}">
              <q-btn :loading="loading" flat dense no-caps icon="vaccines" label="Vacunas"
                     :color="tab === 'vacunas' ? 'white' : 'black'"/>
            </q-tab>
            <q-tab name="desparacitacion" :class="{'bg-indigo text-white': tab === 'desparacitacion'}">
              <q-btn :loading="loading" flat dense no-caps icon="pest_control" label="Desparasitación"
                     :color="tab === 'desparacitacion' ? 'white' : 'black'"/>
            </q-tab>
            <!--            btab fotografias-->
            <q-tab name="fotos" :class="{'bg-grey text-white': tab === 'fotos'}">
              <q-btn :loading="loading" flat dense no-caps icon="photo_camera" label="Fotografias"
                     :color="tab === 'fotos' ? 'white' : 'black'"/>
            </q-tab>
          </q-tabs>
        </q-card>
        <q-card flat bordered>
          <q-tab-panels v-model="tab">
            <q-tab-panel name="mascota" animated>
              <MascotaForm :mascotaData="mascota" @getMascota="getMascota" v-if="mascota.id"/>
            </q-tab-panel>
            <!--            <q-tab-panel name="historial" animated>-->
            <!--              <MascotaHistorial :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="ventas" animated>-->
            <!--              <MascotaSales :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="complementos" animated>-->
            <!--              <MascotaComplementos :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="certificados" animated>-->
            <!--              <MascotaCertificados :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="laboratorios" animated>-->
            <!--              <MascotaLaboratorios :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="vacunas" animated>-->
            <!--              <MascotaVacunas :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="desparacitacion" animated>-->
            <!--              <MascotaDesparacitacion :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
            <!--            <q-tab-panel name="fotos" animated>-->
            <!--              <MascotaFotografias :mascota="mascota" @getMascota="getMascota"/>-->
            <!--            </q-tab-panel>-->
          </q-tab-panels>
          <!--          <pre>{{ mascota }}</pre>-->
        </q-card>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import moment from "moment";
import MascotaForm from "pages/mascotas/MascotaForm.vue";
// import MascotaUpdate from "pages/mascotas/MascotaUpdate.vue";
// import MascotaSales from "pages/mascotas/MascotaSales.vue";
// import MascotaComplementos from "pages/mascotas/MascotaComplemetos.vue";
// import MascotaCertificados from "pages/mascotas/MascotaCertificados.vue";
// import MascotaLaboratorios from "pages/mascotas/MascotaLaboratorios.vue";
// import MascotaVacunas from "pages/mascotas/MascotaVacunas.vue";
// import MascotaDesparacitacion from "pages/mascotas/MascotaDesparacitacion.vue";
// import MascotaHistorial from "pages/mascotas/MascotaHistorial.vue";
// import MascotaFotografias from "pages/mascotas/MascotaFotografias.vue";

export default {
  components: {
    MascotaForm
    // MascotaFotografias,
    // MascotaHistorial,
    // MascotaDesparacitacion,
    // MascotaVacunas,
    // MascotaLaboratorios, MascotaCertificados, MascotaComplementos, MascotaSales, MascotaUpdate
  },
  data() {
    return {
      tab: 'mascota',
      loading: false,
      mascota: {
        nombre: '',
        especie: '',
        raza: '',
        sexo: 'Macho',
        fecha_nac: moment().format('YYYY-MM-DD'),
        senas_particulares: '',
        photo: '/defaultPet.jpg', // Imagen por defecto
        color: '',
        propietario_nombre: '',
        propietario_direccion: '',
        propietario_telefono: '',
        propietario_ciudad: 'Oruro',
        propietario_celular: '',
      },
    }
  },
  created() {
    this.getMascota()
  },
  methods: {
    async getMascota() {
      this.loading = true
      const response = await this.$axios.get(`/mascotas/${this.$route.params.id}`);
      this.mascota = response.data;
      this.loading = false
      // this.mascota.photo = this.$url + 'uploads/' + this.mascota.photo;
      // this.razasDisponibles = this.razas[this.mascota.especie] || [];
    },
  }
}
</script>
