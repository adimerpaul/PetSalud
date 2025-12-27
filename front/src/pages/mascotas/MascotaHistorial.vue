<template>
  <q-card flat bordered>

    <!-- HEADER -->
    <q-card-section class="row items-center ">
      <q-avatar color="primary" text-color="white" icon="history_edu" />
      <div class="col">
        <div class="text-subtitle1 text-weight-bold">Historial Clínico</div>
        <div class="text-caption text-grey-7">
          Registra consultas, signos vitales y tratamientos. Imprime un historial en PDF.
        </div>
      </div>

      <div class="col-auto">
        <q-btn
          outline
          color="primary"
          icon="refresh"
          label="Refrescar"
          no-caps
          :loading="loading"
          @click="load"
          class="q-mr-sm"
        />
        <q-btn
          color="positive"
          icon="add"
          label="Nuevo"
          no-caps
          @click="openCreate"
        />
      </div>
    </q-card-section>

    <q-separator />

    <!-- LISTA -->
    <q-card-section class="q-pa-sm">

      <q-banner v-if="!mascota?.id" rounded class="bg-grey-2 text-grey-8">
        <template v-slot:avatar><q-icon name="info" /></template>
        Esperando datos de la mascota...
      </q-banner>

      <q-list v-else bordered separator class="rounded-borders">
        <q-item v-for="h in historiales" :key="h.id" clickable @click="openEdit(h)">
          <q-item-section>
            <q-item-label class="text-weight-medium">
              {{ fmtDate(h.fecha) }}
              <q-badge v-if="h.tratamientos?.length" class="q-ml-sm" color="primary">
                {{ h.tratamientos.length }} trat.
              </q-badge>
            </q-item-label>

            <q-item-label caption class="ellipsis">
              <b>Diagnóstico:</b> {{ h.diagnostico || '-' }}
            </q-item-label>

            <q-item-label caption class="ellipsis">
              <b>Anamnesis:</b> {{ h.anamnesis || '-' }}
            </q-item-label>
          </q-item-section>

          <q-item-section side top>
            <div class="row q-gutter-xs">
              <q-btn dense flat round icon="print" @click.stop="print(h.id)">
                <q-tooltip>Imprimir</q-tooltip>
              </q-btn>
              <q-btn dense flat round icon="edit" color="primary" @click.stop="openEdit(h)">
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn dense flat round icon="medical_services" color="teal" @click.stop="openTratamientos(h)">
                <q-tooltip>Tratamientos</q-tooltip>
              </q-btn>

              <!--              <q-btn dense flat round icon="delete" color="negative" @click.stop="askDelete(h)">-->
<!--                <q-tooltip>Eliminar</q-tooltip>-->
<!--              </q-btn>-->
            </div>
          </q-item-section>
        </q-item>

        <q-item v-if="!loading && historiales.length === 0">
          <q-item-section>
            <q-item-label class="text-grey-7">No hay historiales registrados.</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>

    </q-card-section>

    <!-- DIALOG FORM -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 980px; max-width: 98vw;">
        <q-card-section class="row items-center ">
          <q-avatar color="primary" text-color="white" :icon="isEdit ? 'edit_note' : 'post_add'" />
          <div class="col">
            <div class="text-subtitle1 text-weight-bold">
              {{ isEdit ? 'Editar Historial' : 'Nuevo Historial' }}
            </div>
            <div class="text-caption text-grey-7">
              Mascota: <b>{{ mascota?.nombre || '-' }}</b> · Propietario: <b>{{ mascota?.propietario_nombre || '-' }}</b>
            </div>
          </div>

          <div class="col-auto">
            <q-btn flat icon="close" round dense @click="closeDialog" />
          </div>
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pa-sm">
          <q-form ref="formHistorial" @submit.prevent="save" class="q-gutter-md">

            <!-- FECHA -->
            <div class="row q-col-gutter-sm">
<!--              <div class="col-12 col-md-3">-->
<!--                <q-input-->
<!--                  v-model="form.fecha"-->
<!--                  type="date"-->
<!--                  outlined-->
<!--                  dense-->
<!--                  stack-label-->
<!--                  label="Fecha"-->
<!--                />-->
<!--              </div>-->

              <div class="col-12 col-md-3">
                <q-input v-model.number="form.peso" type="number" step="0.1" outlined dense stack-label label="Peso (kg)" />
              </div>

              <div class="col-12 col-md-3">
                <q-input v-model="form.tr" outlined dense stack-label label="TR (Temp. rectal)" placeholder="Ej: 38.5" />
              </div>

              <div class="col-12 col-md-3">
                <q-input v-model="form.fc" outlined dense stack-label label="FC (Frecuencia cardíaca)" placeholder="Ej: 120" />
              </div>
            </div>

            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input v-model="form.fr" outlined dense stack-label label="FR (Frecuencia respiratoria)" placeholder="Ej: 30" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.tllc" outlined dense stack-label label="TLLC" placeholder="Ej: 2s" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.thc" outlined dense stack-label label="THC" placeholder="Ej: Normal" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.pulso" outlined dense stack-label label="Pulso" placeholder="Ej: Fuerte" />
              </div>
            </div>

            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input v-model="form.apetito" outlined dense stack-label label="Apetito" placeholder="Ej: Normal" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.cf" outlined dense stack-label label="CF (Condición física)" placeholder="Ej: 3/5" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.mucosidad" outlined dense stack-label label="Mucosidad" placeholder="Ej: Rosada" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="form.esterilizado" outlined dense stack-label label="Esterilizado" placeholder="Sí / No" />
              </div>
            </div>

            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-input v-model="form.desparasitacion" outlined dense stack-label label="Desparasitación" placeholder="Fecha/Detalle" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="form.rayox" outlined dense stack-label label="Rayos X" placeholder="Sí/No/Observación" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="form.ecografia" outlined dense stack-label label="Ecografía" placeholder="Sí/No/Observación" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="form.laboratorio" outlined dense stack-label label="Laboratorio" placeholder="Ej: Hemograma, química..." />
              </div>
            </div>


            <!-- VACUNAS -->
            <q-card flat bordered class="bg-grey-1">
              <q-card-section class="row items-center q-col-gutter-sm">
                <q-icon name="vaccines" />
                <div class="text-subtitle2 text-weight-bold">Vacunas</div>
              </q-card-section>
              <q-separator />
              <q-card-section class="row q-col-gutter-sm">
                <div class="col-12 col-md-2"><q-input v-model="form.parvo"  outlined dense stack-label label="Parvo" /></div>
                <div class="col-12 col-md-2"><q-input v-model="form.hexa"   outlined dense stack-label label="Hexa" /></div>
                <div class="col-12 col-md-2"><q-input v-model="form.octa"   outlined dense stack-label label="Octa" /></div>
                <div class="col-12 col-md-2"><q-input v-model="form.rabica" outlined dense stack-label label="Rábica" /></div>
                <div class="col-12 col-md-2"><q-input v-model="form.triple" outlined dense stack-label label="Triple" /></div>
              </q-card-section>
            </q-card>

            <!-- CAMPOS LARGOS CON MIC -->
            <q-card flat bordered>
              <q-card-section class="row items-center q-col-gutter-sm">
                <q-icon name="notes" />
                <div class="text-subtitle2 text-weight-bold">Clínica</div>
                <q-space />
                <q-badge v-if="mic.active" color="red">🎙 Grabando: {{ mic.field }}</q-badge>
              </q-card-section>
              <q-separator />

              <q-card-section class="q-gutter-md">
                <div>
                  <div class="row items-center q-col-gutter-sm q-mb-xs">
                    <div class="col text-weight-medium">Anamnesis</div>
                    <div class="col-auto">
                      <q-btn
                        outline
                        dense
                        icon="mic"
                        label="Dictar"
                        no-caps
                        :color="mic.active && mic.field==='anamnesis' ? 'negative' : 'primary'"
                        @click="toggleMic('anamnesis')"
                      />
                    </div>
                  </div>
                  <q-input v-model="form.anamnesis" type="textarea" autogrow outlined dense />
                </div>

                <div>
                  <div class="row items-center q-col-gutter-sm q-mb-xs">
                    <div class="col text-weight-medium">Diagnóstico</div>
                    <div class="col-auto">
                      <q-btn
                        outline
                        dense
                        icon="mic"
                        label="Dictar"
                        no-caps
                        :color="mic.active && mic.field==='diagnostico' ? 'negative' : 'primary'"
                        @click="toggleMic('diagnostico')"
                      />
                    </div>
                  </div>
                  <q-input v-model="form.diagnostico" type="textarea" autogrow outlined dense />
                </div>

                <div>
                  <div class="row items-center q-col-gutter-sm q-mb-xs">
                    <div class="col text-weight-medium">Pronóstico</div>
                    <div class="col-auto">
                      <q-btn
                        outline
                        dense
                        icon="mic"
                        label="Dictar"
                        no-caps
                        :color="mic.active && mic.field==='pronostico' ? 'negative' : 'primary'"
                        @click="toggleMic('pronostico')"
                      />
                    </div>
                  </div>
                  <q-input v-model="form.pronostico" type="textarea" autogrow outlined dense />
                </div>

                <div>
                  <div class="row items-center q-col-gutter-sm q-mb-xs">
                    <div class="col text-weight-medium">Observaciones</div>
                    <div class="col-auto">
                      <q-btn
                        outline
                        dense
                        icon="mic"
                        label="Dictar"
                        no-caps
                        :color="mic.active && mic.field==='observaciones' ? 'negative' : 'primary'"
                        @click="toggleMic('observaciones')"
                      />
                    </div>
                  </div>
                  <q-input v-model="form.observaciones" type="textarea" autogrow outlined dense />
                </div>
              </q-card-section>
            </q-card>

            <!-- TRATAMIENTOS -->
<!--            <q-card flat bordered>-->
<!--              <q-card-section class="row items-center q-col-gutter-sm">-->
<!--                <q-icon name="medical_services" />-->
<!--                <div class="text-subtitle2 text-weight-bold">Tratamientos</div>-->
<!--                <q-space />-->
<!--                <q-btn color="primary" icon="add" label="Agregar" no-caps dense @click="addTratamiento" />-->
<!--              </q-card-section>-->

<!--              <q-separator />-->

<!--              <q-card-section class="q-pa-sm">-->
<!--                <q-banner v-if="form.tratamientos.length === 0" rounded class="bg-grey-1 text-grey-8">-->
<!--                  <template v-slot:avatar><q-icon name="info" /></template>-->
<!--                  Sin tratamientos. Presiona “Agregar”.-->
<!--                </q-banner>-->

<!--                <div v-for="(t, i) in form.tratamientos" :key="i" class="q-mb-sm">-->
<!--                  <q-card flat bordered class="bg-grey-1">-->
<!--                    <q-card-section class="row items-center q-col-gutter-sm">-->
<!--                      <div class="col">-->
<!--                        <div class="text-weight-medium">Tratamiento #{{ i + 1 }}</div>-->
<!--                        <div class="text-caption text-grey-7">Medicamento + dosis + frecuencia + duración</div>-->
<!--                      </div>-->
<!--                      <div class="col-auto">-->
<!--                        <q-btn dense flat round icon="delete" color="negative" @click="removeTratamiento(i)" />-->
<!--                      </div>-->
<!--                    </q-card-section>-->

<!--                    <q-separator />-->

<!--                    <q-card-section class="row q-col-gutter-sm">-->
<!--                      <div class="col-12 col-md-4">-->
<!--                        <q-input v-model="t.medicamento" outlined dense stack-label label="Medicamento" />-->
<!--                      </div>-->
<!--                      <div class="col-12 col-md-2">-->
<!--                        <q-input v-model="t.dosis" outlined dense stack-label label="Dosis" />-->
<!--                      </div>-->
<!--                      <div class="col-12 col-md-2">-->
<!--                        <q-input v-model="t.frecuencia" outlined dense stack-label label="Frecuencia" />-->
<!--                      </div>-->
<!--                      <div class="col-12 col-md-2">-->
<!--                        <q-input v-model="t.duracion" outlined dense stack-label label="Duración" />-->
<!--                      </div>-->
<!--                      <div class="col-12 col-md-2">-->
<!--                        <q-input v-model.number="t.costo" type="number" step="0.01" outlined dense stack-label label="Costo" />-->
<!--                      </div>-->

<!--                      <div class="col-12">-->
<!--                        <q-input v-model="t.indicaciones" type="textarea" autogrow outlined dense stack-label label="Indicaciones" />-->
<!--                      </div>-->

<!--                      <div class="col-12 col-md-3">-->
<!--                        <q-toggle v-model="t.pagado" label="Pagado" />-->
<!--                      </div>-->
<!--                    </q-card-section>-->
<!--                  </q-card>-->
<!--                </div>-->
<!--              </q-card-section>-->
<!--            </q-card>-->

            <!-- ACTIONS -->
            <div class="row items-center q-col-gutter-sm">
              <div class="col">
                <q-btn
                  outline
                  color="negative"
                  icon="delete"
                  label="Eliminar"
                  no-caps
                  v-if="isEdit"
                  :loading="saving"
                  @click="askDelete(current)"
                />
              </div>

              <div class="col-auto">
                <q-btn flat label="Cancelar" no-caps @click="closeDialog" :disable="saving" class="q-mr-sm"/>
                <q-btn color="positive" icon="save" :label="isEdit ? 'Actualizar' : 'Guardar'" no-caps type="submit" :loading="saving" />
              </div>
            </div>

          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <tratamientos-dialog
      v-model="dialogTrat"
      :historial="tratHistorial"
      @updated="load"
    />

  </q-card>
</template>

<script>
import moment from 'moment'
import TratamientosDialog from 'components/TratamientosDialog.vue'

export default {
  name: 'MascotaHistorial',
  components: { TratamientosDialog },
  props: {
    mascota: { type: Object, required: true }
  },
  data () {
    return {
      dialogTrat: false,
      tratHistorial: null,
      loading: false,
      saving: false,
      historiales: [],

      dialog: false,
      isEdit: false,
      current: null,

      form: this.blankForm(),

      mic: {
        active: false,
        field: '',
        rec: null
      }
    }
  },
  watch: {
    mascota: {
      deep: true,
      handler () {
        if (this.mascota?.id) this.load()
      }
    }
  },
  mounted () {
    if (this.mascota?.id) this.load()
  },
  methods: {
    openTratamientos (h) {
      this.tratHistorial = h
      this.dialogTrat = true
    },
    blankForm () {
      return {
        id: null,
        mascota_id: null,
        fecha: moment().format('YYYY-MM-DD'),

        peso: null,
        tr: '',
        fc: '',
        fr: '',
        tllc: '',
        thc: '',
        pulso: '',
        apetito: '',
        cf: '',
        mucosidad: '',
        esterilizado: '',
        desparasitacion: '',

        parvo: '',
        hexa: '',
        octa: '',
        rabica: '',
        triple: '',

        rayox: '',
        laboratorio: '',
        ecografia: '',

        anamnesis: '',
        diagnostico: '',
        pronostico: '',
        observaciones: '',

        tratamientos: []
      }
    },

    fmtDate (d) {
      if (!d) return '-'
      return moment(d).format('DD/MM/YYYY')
    },

    load () {
      this.loading = true
      this.$axios.get(`/mascotas/${this.mascota.id}/historiales`)
        .then(({ data }) => { this.historiales = data || [] })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo cargar historiales'))
        .finally(() => { this.loading = false })
    },

    openCreate () {
      this.isEdit = false
      this.current = null
      this.form = this.blankForm()
      this.form.mascota_id = this.mascota.id
      this.dialog = true

      this.$nextTick(() => {
        this.$refs.formHistorial?.resetValidation?.()
      })
    },

    openEdit (h) {
      this.isEdit = true
      this.current = h
      this.form = {
        ...this.blankForm(),
        ...h,
        mascota_id: this.mascota.id,
        tratamientos: (h.tratamientos || []).map(t => ({
          medicamento: t.medicamento || '',
          dosis: t.dosis || '',
          frecuencia: t.frecuencia || '',
          duracion: t.duracion || '',
          indicaciones: t.indicaciones || '',
          costo: t.costo ?? null,
          pagado: !!t.pagado
        }))
      }
      this.dialog = true

      this.$nextTick(() => {
        this.$refs.formHistorial?.resetValidation?.()
      })
    },

    closeDialog () {
      this.stopMic()
      this.dialog = false
    },

    addTratamiento () {
      this.form.tratamientos.push({
        medicamento: '',
        dosis: '',
        frecuencia: '',
        duracion: '',
        indicaciones: '',
        costo: null,
        pagado: false
      })
    },

    removeTratamiento (i) {
      this.form.tratamientos.splice(i, 1)
    },

    save () {
      if (!this.mascota?.id) return
      this.saving = true
      this.stopMic()

      const payload = {
        ...this.form,
        mascota_id: this.mascota.id
      }

      const req = this.isEdit
        ? this.$axios.put(`/historiales/${this.form.id}`, payload)
        : this.$axios.post('/historiales', payload)

      req.then(() => {
        this.$alert.success(this.isEdit ? 'Historial actualizado' : 'Historial guardado')
        this.dialog = false
        this.load()
      })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo guardar'))
        .finally(() => { this.saving = false })
    },

    askDelete (h) {
      if (!h?.id) return
      this.stopMic()
      this.$alert.confirm('¿Eliminar este historial clínico?')
        .onOk(() => this.destroy(h.id))
    },

    destroy (id) {
      this.saving = true
      this.$axios.delete(`/historiales/${id}`)
        .then(() => {
          this.$alert.success('Historial eliminado')
          this.dialog = false
          this.load()
        })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo eliminar'))
        .finally(() => { this.saving = false })
    },

    print (id) {
      window.open(`${this.$url}/historiales/${id}/pdf`, '_blank')
    },

    // =========================
    // MICRÓFONO (Speech-to-text)
    // =========================
    toggleMic (field) {
      if (!field) return

      // si está activo y es el mismo campo => detener
      if (this.mic.active && this.mic.field === field) {
        this.stopMic()
        return
      }

      // si está activo pero otro campo => reiniciar
      this.stopMic()
      this.startMic(field)
    },

    startMic (field) {
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition
      if (!SR) {
        this.$alert.error('Tu navegador no soporta dictado por voz (SpeechRecognition).')
        return
      }

      const rec = new SR()
      rec.lang = 'es-ES'
      rec.interimResults = true
      rec.continuous = true

      this.mic.active = true
      this.mic.field = field
      this.mic.rec = rec

      const baseText = this.form[field] || ''

      rec.onresult = (event) => {
        let transcript = ''
        for (let i = event.resultIndex; i < event.results.length; i++) {
          transcript += event.results[i][0].transcript
        }
        // concat sin borrar lo anterior
        this.form[field] = (baseText ? baseText + ' ' : '') + transcript
      }

      rec.onerror = () => {
        this.stopMic()
      }

      rec.onend = () => {
        // si terminó solo, marcamos off
        this.mic.active = false
        this.mic.field = ''
        this.mic.rec = null
      }

      rec.start()
    },

    stopMic () {
      try {
        if (this.mic.rec) this.mic.rec.stop()
      } catch (e) {}
      this.mic.active = false
      this.mic.field = ''
      this.mic.rec = null
    }
  },
  beforeUnmount () {
    this.stopMic()
  }
}
</script>

<style scoped>
.ellipsis {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
