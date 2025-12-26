<template>
  <q-dialog v-model="model" persistent>
    <q-card style="width: 1100px; max-width: 98vw;">
      <!-- HEADER -->
      <q-card-section class="row items-center q-col-gutter-sm">
        <q-avatar color="teal" text-color="white" icon="medical_services" />
        <div class="col">
          <div class="text-subtitle1 text-weight-bold">Tratamientos</div>
          <div class="text-caption text-grey-7">
            Historial: <b>{{ historial?.id || '-' }}</b> ·
            Fecha: <b>{{ fmtDate(historial?.fecha) }}</b>
          </div>
        </div>
        <div class="col-auto">
          <q-btn flat round dense icon="close" @click="close" />
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section class="q-pa-sm">
        <div class="row q-col-gutter-sm">
          <!-- LISTA -->
          <div class="col-12 col-md-5">
            <q-card flat bordered>
              <q-card-section class="row items-center">
                <div class="text-subtitle2 text-weight-bold">Lista</div>
                <q-space />
                <q-btn
                  color="teal"
                  icon="add"
                  label="Nuevo"
                  no-caps
                  :disable="loading"
                  @click="openCreate"
                />
              </q-card-section>
              <q-separator />
              <q-card-section class="q-pa-sm">
                <q-banner v-if="!loading && items.length === 0" rounded class="bg-grey-2 text-grey-8">
                  <template v-slot:avatar><q-icon name="info" /></template>
                  No hay tratamientos.
                </q-banner>

                <q-list v-else bordered separator class="rounded-borders">
                  <q-item v-for="t in items" :key="t.id" clickable @click="openEdit(t)">
                    <q-item-section>
                      <q-item-label class="text-weight-medium">
                        {{ fmtDate(t.fecha) }}
                        <q-badge class="q-ml-sm" color="teal">
                          Bs {{ money(t.costo) }}
                        </q-badge>
                        <q-badge v-if="t.pagado" class="q-ml-xs" color="positive">Pagado</q-badge>
                      </q-item-label>
                      <q-item-label caption class="ellipsis">
                        <b>Comentario:</b> {{ t.comentario || '-' }}
                      </q-item-label>
                      <q-item-label caption class="ellipsis">
                        <b>Obs:</b> {{ t.observaciones || '-' }}
                      </q-item-label>
                    </q-item-section>

                    <q-item-section side top>
                      <div class="row q-gutter-xs">
                        <q-btn dense flat round icon="edit" color="primary" @click.stop="openEdit(t)">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn dense flat round icon="delete" color="negative" @click.stop="askDelete(t)">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                      </div>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card-section>
            </q-card>
          </div>

          <!-- FORM -->
          <div class="col-12 col-md-7">
            <q-card flat bordered>
              <q-card-section class="row items-center">
                <div class="text-subtitle2 text-weight-bold">
                  {{ isEdit ? 'Editar' : 'Nuevo' }} tratamiento
                </div>
                <q-space />
                <q-btn
                  outline
                  color="primary"
                  icon="refresh"
                  label="Refrescar"
                  no-caps
                  :loading="loading"
                  @click="load"
                />
              </q-card-section>
              <q-separator />

              <q-card-section class="q-pa-sm">
                <q-form @submit.prevent="save" class="q-gutter-md">
                  <div class="row q-col-gutter-sm">
                    <div class="col-12 col-md-4">
                      <q-input v-model="form.fecha" type="date" outlined dense stack-label label="Fecha" />
                    </div>
                    <div class="col-12 col-md-4">
                      <q-toggle v-model="form.pagado" label="Pagado" />
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input :model-value="money(totalProductos)" outlined dense stack-label label="Total productos (Bs)" readonly />
                    </div>
                  </div>

                  <q-input v-model="form.comentario" outlined dense stack-label label="Comentario" />
                  <q-input v-model="form.observaciones" type="textarea" autogrow outlined dense stack-label label="Observaciones" />

                  <!-- PRODUCTOS -->
                  <q-card flat bordered class="bg-grey-1">
                    <q-card-section class="row items-center q-col-gutter-sm">
                      <q-icon name="inventory_2" />
                      <div class="text-subtitle2 text-weight-bold">Productos del tratamiento</div>
                      <q-space />
                      <q-btn color="teal" icon="add" label="Agregar producto" no-caps dense @click="addProducto" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section class="q-pa-sm">
                      <q-banner v-if="form.productos.length === 0" rounded class="bg-white text-grey-8">
                        <template v-slot:avatar><q-icon name="info" /></template>
                        Sin productos. Presiona “Agregar producto”.
                      </q-banner>

                      <div v-for="(p, i) in form.productos" :key="i" class="q-mb-sm">
                        <q-card flat bordered class="bg-white">
                          <q-card-section class="row items-center q-col-gutter-sm">
                            <div class="col">
                              <div class="text-weight-medium">Producto #{{ i + 1 }}</div>
                              <div class="text-caption text-grey-7">
                                Subtotal: Bs {{ money(subtotal(p)) }}
                              </div>
                            </div>
                            <div class="col-auto">
                              <q-btn dense flat round icon="delete" color="negative" @click="removeProducto(i)" />
                            </div>
                          </q-card-section>

                          <q-separator />

                          <q-card-section class="row q-col-gutter-sm">
                            <div class="col-12 col-md-6">
                              <q-select
                                v-model="p.producto"
                                :options="productosOptions"
                                option-label="nombre"
                                option-value="id"
                                emit-value
                                map-options
                                outlined
                                dense
                                stack-label
                                label="Producto"
                                use-input
                                input-debounce="300"
                                @filter="filterProductos"
                              />
                            </div>
                            <div class="col-6 col-md-3">
                              <q-input v-model.number="p.cantidad" type="number" step="0.01" outlined dense stack-label label="Cantidad" />
                            </div>
                            <div class="col-6 col-md-3">
                              <q-input v-model.number="p.precio" type="number" step="0.01" outlined dense stack-label label="Precio (Bs)" />
                            </div>
                          </q-card-section>
                        </q-card>
                      </div>
                    </q-card-section>
                  </q-card>

                  <!-- ACTIONS -->
                  <div class="row items-center q-col-gutter-sm">
                    <div class="col">
                      <q-btn
                        v-if="isEdit"
                        outline
                        color="negative"
                        icon="delete"
                        label="Eliminar"
                        no-caps
                        :loading="saving"
                        @click="askDelete(form)"
                      />
                    </div>

                    <div class="col-auto">
                      <q-btn flat label="Cancelar" no-caps @click="resetForm" :disable="saving" class="q-mr-sm" />
                      <q-btn color="positive" icon="save" :label="isEdit ? 'Actualizar' : 'Guardar'" no-caps type="submit" :loading="saving" />
                    </div>
                  </div>

                </q-form>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </q-card-section>

    </q-card>
  </q-dialog>
</template>

<script>
import moment from 'moment'

export default {
  name: 'TratamientosDialog',
  props: {
    modelValue: { type: Boolean, default: false },
    historial: { type: Object, default: null }
  },
  emits: ['update:modelValue', 'updated'],
  data () {
    return {
      loading: false,
      saving: false,
      items: [],

      isEdit: false,
      productosOptions: [],
      productosAll: [],

      form: this.blank()
    }
  },
  computed: {
    model: {
      get () { return this.modelValue },
      set (v) { this.$emit('update:modelValue', v) }
    },
    totalProductos () {
      return (this.form.productos || []).reduce((acc, p) => acc + this.subtotal(p), 0)
    }
  },
  watch: {
    modelValue (v) {
      if (v) {
        this.load()
        this.loadProductos()
        this.resetForm()
      }
    }
  },
  methods: {
    blank () {
      return {
        id: null,
        fecha: moment().format('YYYY-MM-DD'),
        comentario: '',
        observaciones: '',
        pagado: false,
        productos: []
      }
    },
    fmtDate (d) {
      if (!d) return '-'
      return moment(d).format('DD/MM/YYYY')
    },
    money (n) {
      const x = Number(n || 0)
      return x.toFixed(2)
    },
    subtotal (p) {
      const c = Number(p?.cantidad || 0)
      const pr = Number(p?.precio || 0)
      return c * pr
    },

    close () {
      this.model = false
    },

    async load () {
      if (!this.historial?.id) return
      this.loading = true
      try {
        const { data } = await this.$axios.get(`/historiales/${this.historial.id}/tratamientos`)
        this.items = data || []
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar tratamientos')
      } finally {
        this.loading = false
      }
    },

    async loadProductos () {
      // Ajusta si tu endpoint de productos usa paginación/filters
      try {
        const { data } = await this.$axios.get('/productos', { params: { per_page: 200 } })
        // si tu api devuelve {data:[], ...} cambia acá:
        const list = Array.isArray(data) ? data : (data?.data || [])
        this.productosAll = list
        this.productosOptions = list.slice(0, 50)
      } catch (e) {
        // no bloquear
      }
    },

    filterProductos (val, update) {
      update(() => {
        const needle = (val || '').toLowerCase()
        this.productosOptions = this.productosAll
          .filter(p => (p.nombre || '').toLowerCase().includes(needle))
          .slice(0, 50)
      })
    },

    openCreate () {
      this.isEdit = false
      this.form = this.blank()
    },

    openEdit (t) {
      this.isEdit = true
      this.form = {
        id: t.id,
        fecha: t.fecha || moment().format('YYYY-MM-DD'),
        comentario: t.comentario || '',
        observaciones: t.observaciones || '',
        pagado: !!t.pagado,
        productos: (t.productos || []).map(pp => ({
          id: pp.id,
          producto: pp.producto_id || pp.producto?.id || null,
          cantidad: Number(pp.cantidad || 1),
          precio: Number(pp.precio || 0)
        }))
      }
    },

    resetForm () {
      this.isEdit = false
      this.form = this.blank()
    },

    addProducto () {
      this.form.productos.push({
        producto: null,
        cantidad: 1,
        precio: 0
      })
    },

    removeProducto (i) {
      this.form.productos.splice(i, 1)
    },

    async save () {
      if (!this.historial?.id) return

      // payload para backend (solo ids + cantidad + precio)
      const payload = {
        historial_id: this.historial.id,
        fecha: this.form.fecha,
        comentario: this.form.comentario,
        observaciones: this.form.observaciones,
        pagado: this.form.pagado,
        productos: (this.form.productos || []).map(p => ({
          producto_id: p.producto,
          cantidad: Number(p.cantidad || 0),
          precio: Number(p.precio || 0)
        }))
      }

      this.saving = true
      try {
        if (this.isEdit && this.form.id) {
          await this.$axios.put(`/tratamientos/${this.form.id}`, payload)
          this.$alert.success('Tratamiento actualizado')
        } else {
          await this.$axios.post('/tratamientos', payload)
          this.$alert.success('Tratamiento creado')
        }
        await this.load()
        this.$emit('updated')
        this.resetForm()
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.saving = false
      }
    },

    askDelete (t) {
      const id = t?.id
      if (!id) return
      this.$alert.confirm('¿Eliminar este tratamiento?')
        .onOk(() => this.destroy(id))
    },

    async destroy (id) {
      this.saving = true
      try {
        await this.$axios.delete(`/tratamientos/${id}`)
        this.$alert.success('Tratamiento eliminado')
        await this.load()
        this.$emit('updated')
        this.resetForm()
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo eliminar')
      } finally {
        this.saving = false
      }
    }
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
