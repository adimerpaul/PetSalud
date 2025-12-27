<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
      <q-card-section class="q-pa-xs">
        <q-form @submit.prevent="getVentas">
          <div class="row q-col-gutter-xs">
            <div class="col-12 col-md-2">
              <q-input v-model="fechaInicio" label="Fecha Inicio" type="date" outlined dense />
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="fechaFin" label="Fecha Fin" type="date" outlined dense />
            </div>

            <div class="col-12 col-md-2" v-if="$store.user.role === 'Admin'">
              <q-select
                v-model="user"
                label="Usuario"
                outlined
                dense
                :options="users"
                option-label="name"
                option-value="id"
                emit-value
                map-options
              />
            </div>

            <div class="col-12 col-md-2" v-if="$store.user.role === 'Admin'">
              <q-select
                v-model="tipo"
                label="Tipo"
                outlined
                dense
                :options="tipos"
                emit-value
                map-options
              />
            </div>

            <div class="col-12 col-md-2 flex flex-center">
              <q-btn
                style="width: 150px"
                label="Buscar"
                color="primary"
                type="submit"
                icon="search"
                no-caps
                :loading="loading"
              />
            </div>

            <div class="col-12 col-md-2 flex flex-center" v-if="$store.user.role === 'Admin'">
              <q-btn
                style="width: 150px"
                label="Imprimir"
                color="indigo"
                icon="print"
                no-caps
                :loading="loading"
                @click="printReport"
              />
            </div>

            <div class="col-12 col-md-2 flex flex-center">
              <q-btn
                style="width: 150px"
                label="Excel"
                color="green"
                icon="fa-solid fa-file-excel"
                no-caps
                :loading="loading"
                @click="excelExport"
              />
            </div>

            <div class="col-12 col-md-4 q-mt-sm">
              <q-input v-model="filter" label="Filtro" outlined dense @input="filtroVentas" clearable />
            </div>

            <div class="col-12 col-md-2 flex flex-center q-mt-sm">
              <q-btn
                style="width: 150px"
                label="Venta"
                color="positive"
                @click="$router.push('/ventas/add')"
                no-caps
                icon="add_circle_outline"
                :loading="loading"
              />
            </div>
          </div>
        </q-form>

        <div class="row q-mt-sm" v-if="$store.user.role === 'Admin'">
          <div class="col-12 col-md-4 q-pa-xs">
            <q-list bordered padding dense>
              <q-item>
                <q-item-section avatar>
                  <q-avatar color="indigo" text-color="white" icon="event" />
                </q-item-section>
                <q-item-section>
                  <q-item-label><b>{{ ventas.length }}</b> ventas</q-item-label>
                  <q-item-label caption>Cantidad de ventas</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>

          <div class="col-12 col-md-4 q-pa-xs">
            <q-list bordered padding dense>
              <q-item>
                <q-item-section avatar>
                  <q-avatar color="green" text-color="white" icon="attach_money" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    <b>Bs {{ totalVigentes }}</b>
                  </q-item-label>
                  <q-item-label caption>Total de ventas (vigentes)</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>

          <div class="col-12 col-md-4 q-pa-xs">
            <q-list bordered padding dense>
              <q-item>
                <q-item-section avatar>
                  <q-avatar color="red" text-color="white" icon="cancel" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    <b>Bs {{ totalAnuladas }}</b>
                  </q-item-label>
                  <q-item-label caption>Total de ventas anuladas</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="q-mt-md">
      <q-card-section>
        <q-markup-table wrap-cells dense flat bordered>
          <thead>
          <tr class="bg-orange text-white">
            <th style="width: 120px;">Acciones</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Total</th>
            <th>Cliente</th>
            <th>Usuario</th>
            <th>Detalle</th>
            <th>Comentario</th>
            <th>Pago</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="venta in ventas" :key="venta.id">
            <td>
              <q-btn-dropdown
                label="Opciones"
                no-caps
                dropdown-icon="more_vert"
                color="indigo"
                size="10px"
                v-if="!venta.anulado"
              >
                <q-list>
                  <q-item clickable v-ripple @click="imprimirVenta(venta)" v-close-popup>
                    <q-item-section avatar><q-avatar icon="print" /></q-item-section>
                    <q-item-section><q-item-label>Imprimir</q-item-label></q-item-section>
                  </q-item>
                  <q-separator />
                  <q-item clickable v-ripple @click="anular(venta.id)" v-close-popup>
                    <q-item-section avatar><q-avatar icon="delete" /></q-item-section>
                    <q-item-section><q-item-label>Anular</q-item-label></q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
              <div v-else>
                <q-chip color="red" text-color="white" label="Anulada" dense />
              </div>
            </td>

            <td>{{ formatDate(venta.fecha) }}</td>

            <td>
              <q-chip dense :color="venta.tipo === 'Gasto' ? 'red' : 'green'" text-color="white" size="10px">
                {{ venta.tipo || 'Venta' }}
              </q-chip>
            </td>

            <td class="text-right">{{ Number(venta.total || 0).toFixed(2) }}</td>

            <td>
              {{ venta.nombre || '' }}<br />
              <span class="text-grey-7">{{ venta.mascota?.nombre || '' }}</span>
            </td>

            <td>{{ venta.user?.username || venta.user?.name || '' }}</td>

            <td>
              <div style="max-width: 350px; white-space: normal; line-height: 0.9;">
                <span v-for="detail in (venta.details || [])" :key="detail.id">
                  {{ detail.cantidad }} {{ detail.productoName }},
                </span>
              </div>
            </td>

            <td>
              <div style="max-width: 300px; white-space: normal; line-height: 0.9;">
                {{ venta.comentarioDoctor || '' }}
              </div>
            </td>

            <td>{{ venta.pago || '' }}</td>
          </tr>
          </tbody>
        </q-markup-table>
      </q-card-section>
    </q-card>

    <!-- área oculta para imprimir -->
    <div id="printArea" class="hidden"></div>
  </q-page>
</template>

<script>
import moment from 'moment'
import { Imprimir } from 'src/addons/Imprimir.js'
import { Excel } from 'src/addons/Excel.js'
import { Printd } from 'printd'

export default {
  name: 'VentaIndex',
  data () {
    return {
      fechaInicio: moment().format('YYYY-MM-DD'),
      fechaFin: moment().format('YYYY-MM-DD'),
      ventas: [],
      ventasAll: [],
      loading: false,
      filter: '',
      users: [{ id: '', name: 'Todos' }],
      user: '',
      tipo: '',
      tipos: [
        { label: 'Todos', value: '' },
        { label: 'Venta', value: 'Venta' },
        { label: 'Gasto', value: 'Gasto' }
      ]
    }
  },
  computed: {
    totalVigentes () {
      return this.ventasAll
        .filter(v => !v.anulado)
        .reduce((acc, v) => acc + Number(v.total || 0), 0)
        .toFixed(2)
    },
    totalAnuladas () {
      return this.ventasAll
        .filter(v => v.anulado)
        .reduce((acc, v) => acc + Number(v.total || 0), 0)
        .toFixed(2)
    }
  },
  mounted () {
    this.getVentas()
    this.getUsers()
  },
  methods: {
    formatDate (d) {
      return moment(d).format('DD/MM/YYYY HH:mm')
    },

    getUsers () {
      this.users = [{ id: '', name: 'Todos' }]
      this.$axios.get('/users').then(r => {
        ;(r.data || []).forEach(u => {
          this.users.push({ id: u.id, name: u.name })
        })
      })
    },

    getVentas () {
      this.loading = true
      this.$axios.get('/sales', {
        params: {
          fechaInicio: this.fechaInicio,
          fechaFin: this.fechaFin,
          user_id: this.user || '',
          tipo: this.tipo || ''
        }
      }).then(r => {
        this.ventas = r.data || []
        this.ventasAll = r.data || []
        this.filtroVentas()
      }).finally(() => {
        this.loading = false
      })
    },

    filtroVentas () {
      if (!this.filter) {
        this.ventas = this.ventasAll
        return
      }
      const needle = this.filter.toLowerCase()
      this.ventas = this.ventasAll.filter(v => {
        const a = (v.nombre || '').toLowerCase()
        const b = (v.user?.name || v.user?.username || '').toLowerCase()
        const c = (v.mascota?.nombre || '').toLowerCase()
        return a.includes(needle) || b.includes(needle) || c.includes(needle)
      })
    },

    // ✅ IMPRIMIR NOTA (impresora pequeña)
    imprimirVenta (venta) {
      Imprimir.nota(venta)
    },

    anular (id) {
      this.$q.dialog({
        title: 'Anular Venta',
        message: '¿Está seguro de anular la venta?',
        ok: 'Sí',
        cancel: 'No'
      }).onOk(() => {
        this.loading = true
        this.$axios.put(`/sales/${id}/anular`).then(() => {
          this.getVentas()
        }).finally(() => {
          this.loading = false
        })
      })
    },

    excelExport () {
      const ventasNoAnuladas = this.ventas.filter(v => !v.anulado)

      const data = [
        {
          sheet: 'Ventas',
          columns: [
            { label: 'Fecha', value: row => moment(row.fecha).format('DD/MM/YYYY HH:mm') },
            { label: 'Tipo', value: row => row.tipo || 'Venta' },
            { label: 'Cliente', value: row => row.nombre || '' },
            { label: 'Mascota', value: row => row.mascota?.nombre || '' },
            { label: 'Usuario', value: row => row.user?.username || row.user?.name || '' },
            { label: 'Pago', value: row => row.pago || '' },
            { label: 'Total', value: row => Number(row.total || 0).toFixed(2) },
            { label: 'Comentario', value: row => row.comentarioDoctor || '' },
            { label: 'Detalles', value: row => (row.details || []).map(d => `${d.cantidad} ${d.productoName}`).join(', ') }
          ],
          content: ventasNoAnuladas
        }
      ]

      Excel.export(data, 'Ventas')
    },

    // ✅ REPORTE A4 (Printd)
    printReport () {
      const d = new Printd()
      const ventasNoAnuladas = this.ventas.filter(v => !v.anulado)

      const total = ventasNoAnuladas.reduce((acc, v) => acc + Number(v.total || 0), 0)

      const userName = this.user
        ? (this.users.find(u => u.id === this.user) || {}).name || ''
        : ''

      const styles = `
        <style>
          * { box-sizing: border-box; }
          body { font-family: Arial, sans-serif; margin: 18px; color: #111; }
          .head { display:flex; justify-content:space-between; align-items:flex-end; gap:12px; }
          .title { font-size: 16px; font-weight: 800; margin: 0; }
          .meta { font-size: 12px; color: #444; margin-top: 4px; line-height: 1.3; }
          .badge { display:inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; background:#111; color:#fff; }
          hr { border:0; border-top:1px solid #ddd; margin: 12px 0; }

          table { width: 100%; border-collapse: collapse; }
          th, td { border: 1px solid #222; padding: 6px; vertical-align: top; }
          thead th { background: #111; color: #fff; font-size: 12px; }
          tbody td { font-size: 12px; }
          .right { text-align: right; }
          .small { font-size: 11px; color:#333; }
          tfoot td { font-weight: 800; background: #f3f3f3; }

          @media print { @page { size: A4 portrait; margin: 12mm; } }
        </style>
      `

      const html = `
        <div>
          <div class="head">
            <div>
              <div class="title">REPORTE DE VENTAS</div>
              <div class="meta">
                <span class="badge">Rango</span>
                ${moment(this.fechaInicio).format('DD/MM/YYYY')} - ${moment(this.fechaFin).format('DD/MM/YYYY')}
                ${userName ? `<br><span class="badge">Usuario</span> ${userName}` : ''}
                ${this.tipo ? `<br><span class="badge">Tipo</span> ${this.tipo || ''}` : ''}
              </div>
            </div>
            <div class="meta right">
              Impreso: ${moment().format('DD/MM/YYYY HH:mm')}
            </div>
          </div>

          <hr>

          <table>
            <thead>
              <tr>
                <th style="width:140px;">Fecha</th>
                <th style="width:80px;">Tipo</th>
                <th style="width:160px;">Cliente / Mascota</th>
                <th style="width:120px;">Usuario</th>
                <th>Detalle</th>
                <th style="width:90px;" class="right">Total (Bs)</th>
                <th style="width:95px;">Pago</th>
              </tr>
            </thead>
            <tbody>
              ${
        ventasNoAnuladas.map(v => {
          const detalle = (v.details || [])
            .map(d => `${d.cantidad} ${d.productoName}`)
            .join(', ')

          return `
                    <tr>
                      <td>${moment(v.fecha).format('DD/MM/YYYY HH:mm')}</td>
                      <td>${v.tipo || 'Venta'}</td>
                      <td>
                        ${(v.nombre || '')}
                        ${v.mascota?.nombre ? `<div class="small">${v.mascota.nombre}</div>` : ''}
                      </td>
                      <td>${v.user?.username || v.user?.name || ''}</td>
                      <td class="small">${detalle}</td>
                      <td class="right">${Number(v.total || 0).toFixed(2)}</td>
                      <td>${v.pago || ''}</td>
                    </tr>
                  `
        }).join('')
      }
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="right">TOTAL</td>
                <td class="right">${total.toFixed(2)}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      `

      const el = document.getElementById('printArea')
      el.innerHTML = html
      d.print(el, [styles])
    }
  }
}
</script>


<style scoped>
.hidden { display: none; }
</style>
