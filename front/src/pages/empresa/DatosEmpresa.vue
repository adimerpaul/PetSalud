<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>

      <!-- HEADER -->
      <q-card-section class="row items-center">
        <q-avatar color="primary" text-color="white" icon="business" />
        <div class="q-ml-md">
          <div class="text-h6 text-weight-bold">Datos de Empresa</div>
          <div class="text-caption text-grey-7">
            Configuración de la veterinaria (solo administrador)
          </div>
        </div>
        <q-space />
        <q-btn icon="refresh" flat round @click="load" :loading="loading" />
        <q-btn icon="save" color="positive" label="Guardar" no-caps @click="save" :loading="loading" />
      </q-card-section>

      <q-separator />

      <q-card-section v-if="!isAdmin">
        <q-banner class="bg-orange-1 text-orange-10">
          <q-icon name="lock" class="q-mr-sm" />
          Solo el administrador puede acceder.
        </q-banner>
      </q-card-section>

      <q-card-section v-else>
        <div class="row q-col-gutter-lg">

          <!-- IZQUIERDA -->
          <div class="col-12 col-md-7">
            <q-input v-model="veterinaria.nombre" label="Nombre" outlined dense />
            <q-input v-model="veterinaria.direccion" label="Dirección" outlined dense />
            <div class="row q-col-gutter-md">
              <div class="col-6">
                <q-input v-model="veterinaria.telefono" label="Teléfono" outlined dense />
              </div>
              <div class="col-6">
                <q-input v-model="veterinaria.email" label="Email" outlined dense />
              </div>
            </div>
            <q-input
              v-model="veterinaria.descripcion"
              label="Descripción"
              outlined dense
              type="textarea"
              autogrow
            />
            <q-color v-model="veterinaria.color" />
          </div>

          <!-- DERECHA -->
          <div class="col-12 col-md-5">

            <!-- LOGO -->
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="text-center">
                <q-avatar size="100px" rounded>
                  <q-img
                    v-if="veterinaria.logo"
                    :src="logoUrl"
                    fit="contain"
                  />
                  <q-icon v-else name="pets" size="48px" />
                </q-avatar>

                <div class="q-mt-sm">
                  <q-btn
                    outline
                    icon="photo"
                    label="Cambiar logo"
                    no-caps
                    @click="$refs.logoInput.click()"
                  />
                  <input
                    ref="logoInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="uploadLogo"
                  />
                </div>
              </q-card-section>
            </q-card>

            <!-- IMAGEN -->
<!--            <q-card flat bordered>-->
<!--              <q-card-section class="text-center">-->
<!--                <q-img-->
<!--                  v-if="veterinaria.imagen"-->
<!--                  :src="imagenUrl"-->
<!--                  style="height:140px"-->
<!--                  fit="cover"-->
<!--                  class="rounded-borders"-->
<!--                />-->
<!--                <q-banner v-else class="bg-grey-2 text-grey-7">-->
<!--                  Sin imagen-->
<!--                </q-banner>-->

<!--                <div class="q-mt-sm">-->
<!--                  <q-btn-->
<!--                    outline-->
<!--                    icon="image"-->
<!--                    label="Cambiar imagen"-->
<!--                    no-caps-->
<!--                    @click="$refs.imagenInput.click()"-->
<!--                  />-->
<!--                  <input-->
<!--                    ref="imagenInput"-->
<!--                    type="file"-->
<!--                    accept="image/*"-->
<!--                    class="hidden"-->
<!--                    @change="uploadImagen"-->
<!--                  />-->
<!--                </div>-->
<!--              </q-card-section>-->
<!--            </q-card>-->

          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { setCssVar } from 'quasar'

export default {
  data () {
    return {
      loading: false,
      veterinaria: {}
    }
  },
  computed: {
    isAdmin () {
      return ['admin', 'administrador'].includes(
        (this.$store.user?.role || '').toLowerCase()
      )
    },
    logoUrl () {
      return this.veterinaria.logo
        ? `${this.$url}/../storage/veterinarias/${this.veterinaria.logo}`
        : ''
    },
    imagenUrl () {
      return this.veterinaria.imagen
        ? `${this.$url}/storage/veterinarias/${this.veterinaria.imagen}`
        : ''
    }
  },
  mounted () {
    // if (this.isAdmin)
    this.load()
  },
  methods: {
    load () {
      this.loading = true
      this.$axios.get('/veterinaria')
        .then(r => this.veterinaria = r.data || {})
        .finally(() => this.loading = false)
    },
    save () {
      this.loading = true
      this.$axios.put('/veterinaria', this.veterinaria)
        .then(() => {
          this.$alert.success('Datos guardados correctamente')
          localStorage.setItem('veterinaria_color', this.veterinaria.color)
          setCssVar('primary', this.veterinaria.color)
        })
        .finally(() => this.loading = false)
    },
    uploadLogo (e) {
      const fd = new FormData()
      fd.append('logo', e.target.files[0])
      this.$axios.post('/veterinaria/logo', fd)
        .then(r => this.veterinaria = r.data)
    },
    uploadImagen (e) {
      const fd = new FormData()
      fd.append('imagen', e.target.files[0])
      this.$axios.post('/veterinaria/imagen', fd)
        .then(r => this.veterinaria = r.data)
    }
  }
}
</script>
