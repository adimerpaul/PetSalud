<template>
  <q-card flat bordered>
    <q-form @submit.prevent="submit">
      <q-card-section class="row items-center">
        <q-btn @click="$router.push('/mascotas')" label="Atrás" color="primary" icon="arrow_back" no-caps size="10px" />

        <div class="text-h6 q-mt-sm">{{ isEdit ? 'Editar Mascota' : 'Nueva Mascota' }}</div>
        <q-space/>
        <q-btn v-if="isEdit" @click="imprimirMacota()" label="Imprimir Ficha" color="secondary" icon="print" no-caps size="10px" class="q-ml-sm"/>


        <q-card flat bordered class="q-mt-sm">
          <q-card-section>
            <div class="row q-col-gutter-xs">

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Nombre</label>
                <q-input v-model="mascota.nombre" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Apellido</label>
                <q-input v-model="mascota.apellido" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Especie</label>
                <q-input v-model="mascota.especie" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Raza</label>
                <q-input v-model="mascota.raza" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Sexo</label>
                <div>
                  <q-radio v-model="mascota.sexo" val="Macho" label="Macho" dense />
                  <q-radio v-model="mascota.sexo" val="Hembra" label="Hembra" dense />
                </div>
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Fecha de Nacimiento</label>
                <q-input v-model="mascota.fecha_nac" outlined dense clearable type="date" @update:modelValue="calcEdad"/>
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Foto</label>
                <div>
                  <q-img :src="photoPreview" width="80px" height="80px" @click="$refs.fileInput.click()">
                    <q-icon name="camera_alt" class="absolute-bottom-right cursor-pointer"/>
                  </q-img>
                  <input type="file" class="hidden" ref="fileInput" accept="image/*" @change="onFileChange"/>
<!--                  <pre>{{photoPreview}}</pre>-->
                </div>
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Señas Particulares</label>
                <q-input v-model="mascota.senas_particulares" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Color</label>
                <q-input v-model="mascota.color" outlined dense clearable />
              </div>

              <div class="col-12 col-md-2">
                <label class="text-subtitle2">Edad</label>
                <q-input v-model="mascota.edad" outlined dense clearable />
              </div>

            </div>
          </q-card-section>
        </q-card>

        <q-card flat bordered class="q-mt-sm">
          <q-card-section>
            <div class="text-h6">Datos del propietario</div>

            <div class="row q-col-gutter-xs q-mt-xs">
              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Nombre del Propietario</label>
                <q-input v-model="mascota.propietario_nombre" outlined dense clearable />
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">CI del Propietario</label>
                <q-input v-model="mascota.propietario_ci" outlined dense clearable />
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Dirección del Propietario</label>
                <q-input v-model="mascota.propietario_direccion" outlined dense clearable />
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Numero de contacto</label>
                <q-input v-model="mascota.propietario_telefono" outlined dense clearable />
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Ciudad del Propietario</label>
                <q-select v-model="mascota.propietario_ciudad" outlined dense :options="ciudades"/>
              </div>

              <div class="col-12 col-md-3">
                <label class="text-subtitle2">Celular</label>
                <q-input v-model="mascota.propietario_celular" outlined dense clearable />
              </div>

              <div class="col-12 text-right q-mt-sm">
                <q-btn type="submit" :label="isEdit ? 'Actualizar' : 'Guardar'" color="green" :loading="loading" icon="save" no-caps/>
              </div>
            </div>
          </q-card-section>
        </q-card>

      </q-card-section>
    </q-form>
  </q-card>
</template>

<script>
import moment from 'moment'

export default {
  name: 'MascotaForm',
  props: {
    mascotaData: {
      type: Object,
      default: null,
      required: false
    }
  },
  data () {
    return {
      loading: false,
      mascota: {
        nombre: '',
        apellido: '',
        especie: '',
        raza: '',
        sexo: 'Macho',
        fecha_nac: moment().format('YYYY-MM-DD'),
        edad: '',
        senas_particulares: '',
        color: '',
        propietario_nombre: '',
        propietario_ci: '',
        propietario_direccion: '',
        propietario_telefono: '',
        propietario_ciudad: 'Oruro',
        propietario_celular: '',
        photo: null
      },
      file: null,
      previewImage: null,
      ciudades: ['La Paz','Cochabamba','Santa Cruz','Oruro','Potosí','Chuquisaca','Tarija','Beni','Pando']
    }
  },
  computed: {
    isEdit () {
      return !!this.$route.params.id
    },
    photoPreview () {
      if (this.previewImage) return this.previewImage
      if (this.mascota.photo && typeof this.mascota.photo === 'string') return this.mascota.photo
      return '/defaultPet.jpg'
    }
  },
  created () {
    if (this.isEdit) this.load()
    this.calcEdad()
  },
  methods: {
    imprimirMacota () {
      // <!--        Route::get('/mascotas/{id}/pdf', [MascotaController::class, 'pdf']);-->
      const url = `${this.$url}/mascotas/${this.$route.params.id}/pdf`
      window.open(url, '_blank')
    },
    calcEdad () {
      if (!this.mascota.fecha_nac) return
      const today = moment()
      const birth = moment(this.mascota.fecha_nac)
      this.mascota.edad = today.diff(birth, 'years') + ' años'
    },

    onFileChange (e) {
      const file = e.target.files?.[0]
      if (!file) return
      this.file = file
      this.previewImage = URL.createObjectURL(file)
    },

    load () {
      // this.loading = true
      // this.$axios.get(`/mascotas/${this.$route.params.id}`)
      //   .then(({ data }) => {
      //     this.mascota = data || this.mascota
      //     if (this.mascota.photo) {
      //       this.mascota.photo = `${this.$url}/../uploads/${this.mascota.photo}`
      //     }
      //   })
      //   .finally(() => { this.loading = false })
      if (this.mascotaData) {
        this.mascota = { ...this.mascotaData }
        if (this.mascota.photo) {
          this.mascota.photo = `${this.$url}/../uploads/${this.mascota.photo}`
        }
      }
    },

    submit () {
      this.loading = true

      const fd = new FormData()
      Object.keys(this.mascota).forEach(k => {
        if (k === 'photo') return
        fd.append(k, this.mascota[k] ?? '')
      })
      if (this.file) fd.append('photo', this.file)

      const req = this.isEdit
        ? this.$axios.post(`/mascotas/${this.$route.params.id}`, fd) // update multipart
        : this.$axios.post('/mascotas', fd)

      req.then(() => {
        this.$alert.success(this.isEdit ? 'Mascota actualizada' : 'Mascota creada')
        // emit
        this.$emit('getMascota')
        // this.$router.push('/mascotas')
      })
        .catch(() => this.$alert.error('Error al guardar'))
        .finally(() => { this.loading = false })
    }
  },
  beforeUnmount () {
    if (this.previewImage) URL.revokeObjectURL(this.previewImage)
  }
}
</script>
