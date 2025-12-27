<template>
  <q-page class="q-pa-xs">
    <div class="row">
      <div class="col-12">
        <q-btn
          label="Atras"
          color="primary"
          @click="$router.push('/ventas')"
          no-caps
          icon="arrow_back"
          size="10px"
        />
      </div>

      <!-- LISTA PRODUCTOS -->
      <div class="col-12 col-md-7">
        <q-card flat bordered class="q-ma-xs">
          <q-card-section>
            <q-form @submit.prevent="getProductos">
              <div class="row q-col-gutter-xs">
                <div class="col-12 col-md-7">
                  <q-input v-model="buscarProducto" label="Buscar producto" outlined dense debounce="300" clearable />
                </div>
                <div class="col-12 col-md-3">
                  <q-select v-model="filterTipo" label="Tipo" outlined dense :options="tipos" />
                </div>
                <div class="col-12 col-md-2 flex flex-center">
                  <q-btn label="Buscar" color="primary" no-caps icon="search" size="10px" type="submit" />
                </div>
              </div>
            </q-form>

            <div class="row q-mt-sm">
              <div class="col-12 flex flex-center">
                <q-pagination
                  v-if="totalPages > 1"
                  v-model="currentPage"
                  :max="totalPages"
                  :max-pages="5"
                  boundary-numbers
                  color="black"
                  @input="getProductos"
                />
              </div>
            </div>

            <q-markup-table dense bordered flat>
              <thead>
              <tr>
                <th>Cod</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Stock</th>
              </tr>
              </thead>
              <tbody>
              <tr
                v-for="product in productos"
                :key="product.id"
                @click="agregarAlCarrito(product)"
                style="cursor: pointer"
              >
                <td>{{ product.codigo }}</td>
                <td>{{ product.nombre }}</td>
                <td>
                  <q-chip dense :color="getColor(product.tipo)" size="10px">
                    {{ product.tipo }}
                  </q-chip>
                </td>
                <td class="text-right">{{ Number(product.precioVenta || 0).toFixed(2) }}</td>
                <td>{{ product.stock }}</td>
              </tr>
              </tbody>
            </q-markup-table>
          </q-card-section>
        </q-card>
      </div>

      <!-- CARRITO -->
      <div class="col-12 col-md-5">
        <q-card flat bordered class="q-ma-xs">
          <q-card-section>
            <div class="text-h6 row items-center">
              <div>
                Carrito
                <q-btn
                  icon="history"
                  color="blue"
                  dense
                  @click="recuperarTratamiento"
                  :loading="loading"
                  size="10px"
                  label="Recuperar Tratamiento"
                  no-caps
                />
              </div>
              <q-space />
              <q-btn
                icon="delete"
                color="red"
                dense
                @click="carrito = []"
                :loading="loading"
                size="10px"
                label="Limpiar"
                no-caps
              />
            </div>

            <div class="text-caption">Productos agregados al carrito</div>

            <q-markup-table wrap-cells dense flat bordered>
              <thead>
              <tr class="bg-primary text-white">
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
                <th>Acciones</th>
              </tr>
              </thead>

              <tbody>
              <tr v-for="(item, index) in carrito" :key="index">
                <td>
                  <div style="max-width: 150px; white-space: normal; line-height: 0.9">
                    {{ item.nombre }}
                  </div>
                </td>

                <td>
                  <input v-model.number="item.cantidadVenta" type="number" style="width: 50px" min="1" />
                </td>

                <td>
                  <input v-model.number="item.precioVenta" type="number" style="width: 80px" min="0" step="0.1" />
                </td>

                <td class="text-right">{{ (Number(item.cantidadVenta) * Number(item.precioVenta)).toFixed(2) }} Bs</td>

                <td>
                  <q-btn icon="delete" color="red" dense @click="eliminarDelCarrito(index)" :loading="loading" size="10px" />
                </td>
              </tr>
              </tbody>

              <tfoot>
              <tr>
                <td colspan="3" class="text-right text-bold">Total</td>
                <td class="text-bold text-right">{{ totalVenta }} Bs</td>
                <td></td>
              </tr>
              </tfoot>
            </q-markup-table>

            <q-btn
              label="Realizar Venta"
              color="positive"
              class="full-width q-mt-md"
              no-caps
              @click="realizarVenta"
              :loading="loading"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- DIALOG VENTA -->
    <q-dialog v-model="dialogVenta">
      <q-card flat bordered style="max-width: 750px;">
        <q-card-section class="q-pb-none row items-center">
          <div class="text-bold">Realizar Venta</div>
          <q-space />
          <q-btn flat dense round icon="close" @click="dialogVenta = false" />
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="realizarVentaPost">
            <div class="row q-col-gutter-xs">
              <div class="col-12 q-pa-xs">
                <div class="row q-col-gutter-xs">
                  <div class="col-12 col-md-6">
                    <q-select
                      input-debounce="300"
                      clearable
                      use-input
                      v-model="mascota"
                      label="Mascota"
                      outlined
                      dense
                      :options="mascotas"
                      :option-label="m => m.id + '|' + m.nombre + '|' + m.propietario_nombre"
                      :option-value="m => m.id"
                      @filter="mascotasFilter"
                      :rules="[val => !!val || 'Campo requerido']"
                    />
                  </div>

                  <div class="col-6 col-md-2 text-center">
                    <q-select
                      v-model="venta.pago"
                      label="Pago"
                      outlined
                      dense
                      :options="['Efectivo', 'Qr', 'Transferencia']"
                    />
                  </div>

                  <div class="col-6 col-md-2 flex flex-center">
                    <q-btn dense color="green" label="Crear" no-caps icon="add_circle_outline" @click="dialogCrearMascota = true" />
                  </div>

                  <div class="col-12 col-md-2 flex flex-center">
                    <q-btn
                      :loading="loading"
                      dense
                      color="blue"
                      label="historial"
                      no-caps
                      icon="history"
                      @click="mascotaHistorial"
                      v-if="mascota && mascota.id"
                    />
                  </div>
                </div>
              </div>

              <div class="col-12">
                <q-input v-model="venta.comentarioDoctor" label="Comentario Doctor" outlined dense type="textarea" rows="2" />
              </div>

              <div class="col-12">
                <q-markup-table wrap-cells dense flat bordered>
                  <thead>
                  <tr class="bg-primary text-white">
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                  </tr>
                  </thead>

                  <tbody>
                  <tr v-for="(item, index) in carrito" :key="index">
                    <td>{{ item.nombre }}</td>
                    <td>{{ item.cantidadVenta }}</td>
                    <td>
                      <q-chip dense :color="getColor(item.tipo)" size="10px">
                        {{ item.tipo }}
                      </q-chip>
                    </td>
                    <td class="text-right">{{ Number(item.precioVenta || 0).toFixed(2) }} Bs</td>
                    <td class="text-right">{{ (Number(item.cantidadVenta) * Number(item.precioVenta)).toFixed(2) }} Bs</td>
                  </tr>
                  </tbody>

                  <tfoot>
                  <tr>
                    <td colspan="4" class="text-right text-bold">Total</td>
                    <td class="text-bold text-right">{{ totalVenta }} Bs</td>
                  </tr>
                  </tfoot>
                </q-markup-table>
              </div>

              <div class="col-12 q-pa-xs">
                <q-btn label="Realizar Venta" color="positive" class="full-width" type="submit" no-caps :loading="loading" />
              </div>

              <!-- HISTORIAL COMPRAS -->
              <div class="col-12" v-if="historialCompra.length > 0">
                <div class="text-h6 row items-center">Historial de Compras</div>

                <q-markup-table flat bordered dense>
                  <thead>
                  <tr>
                    <td>#</td>
                    <td>Fecha</td>
                    <td>Total</td>
                    <td>Anulado</td>
                    <td>Detalles</td>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in historialCompra" :key="index">
                    <td>{{ item.id }}</td>
                    <td>{{ (item.fecha || '').slice(0, 10) }}</td>
                    <td>{{ item.total }}</td>
                    <td>{{ item.anulado ? 'Si' : 'No' }}</td>
                    <td>
                      <div style="max-width: 150px; white-space: normal; line-height: 0.9">
                          <span v-for="(detail, idx) in (item.details || [])" :key="idx">
                            {{ detail.productoName }} ({{ detail.cantidad }})<br>
                          </span>
                      </div>
                    </td>
                  </tr>
                  </tbody>
                </q-markup-table>
              </div>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- DIALOG RECUPERAR TRATAMIENTO -->
    <q-dialog v-model="recuperarTratamientoDialog">
      <q-card flat bordered style="min-width: 600px; max-width: 90vw;">
        <q-card-section class="q-pb-none row items-center">
          <div class="text-bold">Recuperar Tratamiento</div>
          <q-space />
          <q-btn flat dense round icon="close" @click="recuperarTratamientoDialog = false" />
        </q-card-section>

        <q-card-section>
          <div class="text-caption">Seleccione una fecha para recuperar el tratamiento</div>

          <q-input v-model="recuperarTratamientoFecha" type="date" outlined dense>
            <template v-slot:append>
              <q-btn dense color="green" label="Buscar" no-caps icon="search" @click="buscarTratamiento" :loading="loading" />
            </template>
          </q-input>

          <div class="text-caption q-mt-md">
            Tratamientos realizados el {{ recuperarTratamientoFecha }}:
          </div>

          <q-markup-table flat bordered dense>
            <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Doctor</th>
              <th>Mascota</th>
              <th>Costo</th>
              <th>Medicamentos</th>
              <th>Opciones</th>
              <th>Pagado</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="(item, index) in historialTratamientos" :key="index">
              <td>{{ item.id }}</td>
              <td>{{ item.fecha}}</td>
              <td>{{ item.user && item.user.username ? item.user.username : 'Desconocido' }}</td>

              <td>
                <div style="max-width: 150px; white-space: normal; line-height: 0.9">
                  {{ item.historial && item.historial.mascota ? item.historial.mascota.nombre : 'Desconocido' }}
                  ({{ item.historial && item.historial.mascota ? item.historial.mascota.propietario_nombre : 'Desconocido' }})
                </div>
              </td>

              <td>{{ item.costo }} Bs</td>

              <td>
                <div style="max-width: 200px; white-space: normal; line-height: 1.2">
                    <span v-for="(med, idx) in (item.productos || [])" :key="idx">
                      {{ med.producto.nombre }} ({{ med.cantidad }} x {{ med.precio }} Bs) <br />
<!--                      <pre>{{med}}</pre>-->
                    </span>
                </div>
              </td>

              <td>
                <q-btn
                  label="Recuperar"
                  no-caps
                  dense
                  color="green"
                  icon="add_circle_outline"
                  size="10px"
                  @click="agregarAcarritoTratamiento(item)"
                />
              </td>

              <td>
                <q-toggle
                  v-model="item.pagado"
                  :true-value="1"
                  :false-value="0"
                  color="green"
                  size="20px"
                  @input="togglePagado(item)"
                />
              </td>
            </tr>
            </tbody>
          </q-markup-table>
<!--          <pre>{{historialTratamientos}}</pre>-->
<!--          [-->
<!--          {-->
<!--          "id": 1,-->
<!--          "historial_clinico_id": 1,-->
<!--          "user_id": 1,-->
<!--          "fecha": "2025-12-26 21:24:58",-->
<!--          "observaciones": null,-->
<!--          "comentario": null,-->
<!--          "costo": "0.00",-->
<!--          "pagado": 0,-->
<!--          "productos": [-->
<!--          {-->
<!--          "id": 3,-->
<!--          "tratamiento_id": 1,-->
<!--          "producto_id": 815,-->
<!--          "cantidad": 1,-->
<!--          "precio": "0.00",-->
<!--          "producto": {-->
<!--          "id": 815,-->
<!--          "codigo": "ialgo400-1",-->
<!--          "nombre": "Algodón donde 400 gramos alcoa ",-->
<!--          "presentacion": null,-->
<!--          "contenido": null,-->
<!--          "precioCompra": "43.00",-->
<!--          "precioVenta": "60.00",-->
<!--          "stock": 4,-->
<!--          "activo": 1,-->
<!--          "tipo": "Producto",-->
<!--          "imagen": "imagenesdefault.png"-->
<!--          }-->
<!--          },-->
<!--          {-->
<!--          "id": 4,-->
<!--          "tratamiento_id": 1,-->
<!--          "producto_id": 858,-->
<!--          "cantidad": 1,-->
<!--          "precio": "0.00",-->
<!--          "producto": {-->
<!--          "id": 858,-->
<!--          "codigo": "ankfn",-->
<!--          "nombre": "Ankofen 50 ml",-->
<!--          "presentacion": null,-->
<!--          "contenido": null,-->
<!--          "precioCompra": "1.10",-->
<!--          "precioVenta": "2.00",-->
<!--          "stock": 70,-->
<!--          "activo": 1,-->
<!--          "tipo": "Producto",-->
<!--          "imagen": "imagenesdefault.png"-->
<!--          }-->
<!--          }-->
<!--          ],-->
<!--          "user": {-->
<!--          "id": 1,-->
<!--          "name": "Admin User",-->
<!--          "username": "admin",-->
<!--          "role": "Administrador",-->
<!--          "avatar": "defaultAvatar.png",-->
<!--          "email": "",-->
<!--          "email_verified_at": null,-->
<!--          "veterinaria_id": 1-->
<!--          },-->
<!--          "historial": {-->
<!--          "id": 1,-->
<!--          "mascota_id": 1,-->
<!--          "user_id": 1,-->
<!--          "veterinaria_id": 1,-->
<!--          "peso": 23,-->
<!--          "tr": null,-->
<!--          "fc": null,-->
<!--          "fr": null,-->
<!--          "tllc": null,-->
<!--          "thc": null,-->
<!--          "pulso": null,-->
<!--          "apetito": null,-->
<!--          "cf": null,-->
<!--          "mucosidad": null,-->
<!--          "esterilizado": null,-->
<!--          "desparasitacion": null,-->
<!--          "parvo": null,-->
<!--          "hexa": null,-->
<!--          "octa": null,-->
<!--          "rabica": null,-->
<!--          "triple": null,-->
<!--          "rayox": null,-->
<!--          "laboratorio": null,-->
<!--          "ecografia": null,-->
<!--          "anamnesis": null,-->
<!--          "diagnostico": null,-->
<!--          "pronostico": null,-->
<!--          "observaciones": null,-->
<!--          "fecha": "2025-12-26",-->
<!--          "deleted_at": null,-->
<!--          "created_at": "2025-12-27T01:24:47.000000Z",-->
<!--          "updated_at": "2025-12-27T01:24:47.000000Z",-->
<!--          "mascota": {-->
<!--          "id": 1,-->
<!--          "nombre": "Lesly",-->
<!--          "apellido": "Deckow",-->
<!--          "especie": "Conejo",-->
<!--          "raza": null,-->
<!--          "sexo": "Macho",-->
<!--          "fecha_nac": "2020-12-29",-->
<!--          "edad": "4.9942242435458 años",-->
<!--          "senas_particulares": null,-->
<!--          "color": "Manchado",-->
<!--          "photo": "defaultPet.jpg",-->
<!--          "propietario_nombre": "Ellie Towne",-->
<!--          "propietario_ci": "27891877",-->
<!--          "propietario_direccion": "5378 Becker Isle Apt. 417\nTremouth, WV 36231",-->
<!--          "propietario_telefono": "910.599.6974",-->
<!--          "propietario_ciudad": "Tarija",-->
<!--          "propietario_celular": "757-205-0589",-->
<!--          "veterinaria_id": 1-->
<!--          }-->
<!--          }-->
<!--          }-->
<!--          ]-->
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- DIALOG CREAR MASCOTA -->
    <q-dialog v-model="dialogCrearMascota" persistent>
      <q-card style="width: 800px; max-width: 90vw;">
        <q-card-section>
          <div class="text-h6">Registrar Mascota</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
          <q-form @submit.prevent="registrarMascota">
            <div class="row q-col-gutter-md">
              <div class="col-6">
                <q-input v-model="nuevaMascota.nombre" label="Nombre" outlined dense :rules="[val => !!val || 'Campo requerido']" />
              </div>
              <div class="col-6">
                <q-input v-model="nuevaMascota.propietario_nombre" label="Propietario" outlined dense :rules="[val => !!val || 'Campo requerido']" />
              </div>
              <div class="col-6">
                <q-input v-model="nuevaMascota.propietario_telefono" label="Teléfono" outlined dense />
              </div>
              <div class="col-6">
                <q-select v-model="nuevaMascota.propietario_ciudad" label="Ciudad" outlined dense :options="ciudades" />
              </div>
              <div class="col-12">
                <q-input v-model="nuevaMascota.especie" label="Especie" outlined dense />
              </div>
            </div>

            <q-card-actions align="right" class="q-mt-md">
              <q-btn flat label="Cancelar" color="negative" @click="dialogCrearMascota = false" />
              <q-btn label="Guardar" type="submit" color="positive" :loading="loading" />
            </q-card-actions>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <div id="myElement"></div>
  </q-page>
</template>

<script>
import moment from 'moment'
import { Imprimir } from 'src/addons/Imprimir.js'

export default {
  name: 'VentaAdd',
  data () {
    return {
      venta: { comentarioDoctor: '', pago: 'Efectivo' },
      productos: [],
      carrito: [],
      buscarProducto: '',
      currentPage: 1,
      totalPages: 1,
      filterTipo: 'Todos',
      tipos: ['Todos', 'Cirugía', 'Laboratorio', 'Peluqueria', 'Producto', 'Tratamiento'],

      dialogVenta: false,
      loading: false,

      mascotas: [],
      mascota: null,
      historialCompra: [],

      recuperarTratamientoDialog: false,
      recuperarTratamientoFecha: moment().format('YYYY-MM-DD'),
      historialTratamientos: [],

      dialogCrearMascota: false,
      nuevaMascota: {
        nombre: '',
        especie: '',
        propietario_nombre: '',
        propietario_telefono: '',
        propietario_ciudad: 'Oruro'
      },
      ciudades: ['Oruro', 'La Paz', 'Santa Cruz', 'Cochabamba', 'Sucre', 'Tarija', 'Potosí', 'Beni', 'Pando', 'Chuquisaca']
    }
  },

  computed: {
    totalVenta () {
      const total = this.carrito.reduce((acc, item) => acc + (Number(item.cantidadVenta) * Number(item.precioVenta)), 0)
      return total.toFixed(2)
    }
  },

  mounted () {
    this.getProductos()
    this.mascotasGet()

    // Default “SN” como tenías
    this.mascota = { id: 1, nombre: 'SN', propietario_nombre: 'SN' }
  },

  methods: {
    getColor (tipo) {
      switch (tipo) {
        case 'Cirugía': return 'red'
        case 'Laboratorio': return 'blue'
        case 'Peluqueria': return 'green'
        case 'Producto': return 'orange'
        case 'Tratamiento': return 'purple'
        default: return 'grey'
      }
    },

    getProductos () {
      this.$axios.get('/productos', {
        params: {
          filter: this.buscarProducto,
          page: this.currentPage,
          limit: 20,
          tipo: this.filterTipo === 'Todos' ? '' : this.filterTipo
        }
      }).then(r => {
        this.productos = (r.data && r.data.data) ? r.data.data : []
        this.totalPages = r.data && r.data.last_page ? r.data.last_page : 1
      })
    },

    agregarAlCarrito (producto) {
      const item = this.carrito.find(p => p.id === producto.id)
      if (item) {
        item.cantidadVenta = Number(item.cantidadVenta) + 1
      } else {
        this.carrito.push({
          ...producto,
          cantidadVenta: 1,
          precioVenta: Number(producto.precioVenta || 0)
        })
      }
    },

    eliminarDelCarrito (index) {
      this.carrito.splice(index, 1)
    },

    realizarVenta () {
      if (!this.carrito.length) {
        this.$alert.error('El carrito está vacío', 'Error')
        return
      }
      this.dialogVenta = true
      this.venta = { comentarioDoctor: '', pago: 'Efectivo' }
    },

    realizarVentaPost () {
      if (!this.carrito.length) {
        this.$alert.error('El carrito está vacío')
        return
      }
      if (!this.mascota || !this.mascota.id) {
        this.$alert.error('Seleccione una mascota')
        return
      }

      this.loading = true
      this.$axios.post('/sales', {
        mascota: this.mascota,
        total: parseFloat(this.totalVenta),
        productos: this.carrito,
        comentarioDoctor: this.venta.comentarioDoctor,
        pago: this.venta.pago,
        tipo: 'Venta'
      }).then(res => {
        Imprimir.nota(res.data.sale)

        this.mascota = { id: 1, nombre: 'SN', propietario_nombre: 'SN' }
        this.dialogVenta = false
        this.carrito = []
        this.$alert.success('Venta realizada con éxito', 'Éxito')
      }).catch(err => {
        this.$alert.error(err?.response?.data?.message || 'Error al registrar venta', 'Error')
      }).finally(() => {
        this.loading = false
      })
    },

    // ---- MASCOTAS ----
    mascotasGet () {
      this.$axios.get('mascotas', { params: { filter: '' } }).then(r => {
        this.mascotas = r.data && r.data.data ? r.data.data : []
      })
    },

    mascotasFilter (val, update) {
      update(() => {
        this.$axios.get('mascotas', { params: { filter: val || '' } }).then(r => {
          this.mascotas = r.data && r.data.data ? r.data.data : []
        })
      })
    },

    registrarMascota () {
      this.loading = true
      this.$axios.post('/mascotas', this.nuevaMascota).then(res => {
        this.$alert.success('Mascota registrada correctamente')
        this.dialogCrearMascota = false

        this.mascotasGet()
        this.mascota = res.data

        this.nuevaMascota = {
          nombre: '',
          especie: '',
          propietario_nombre: '',
          propietario_telefono: '',
          propietario_ciudad: 'Oruro'
        }
      }).catch(() => {
        this.$alert.error('No se pudo registrar la mascota')
      }).finally(() => {
        this.loading = false
      })
    },

    mascotaHistorial () {
      this.loading = true
      this.$axios.post('mascotas/historial', { mascotaId: this.mascota.id }).then(r => {
        this.historialCompra = r.data || []
      }).finally(() => {
        this.loading = false
      })
    },

    // ---- RECUPERAR / RESTAURAR TRATAMIENTO ----
    recuperarTratamiento () {
      this.recuperarTratamientoDialog = true
      this.recuperarTratamientoFecha = moment().format('YYYY-MM-DD')
      this.buscarTratamiento()
    },

    buscarTratamiento () {
      if (!this.recuperarTratamientoFecha) {
        this.$alert.error('Seleccione una fecha', 'Error')
        return
      }
      this.loading = true
      this.$axios.get('tratamientos', { params: { fecha: this.recuperarTratamientoFecha } })
        .then(r => {
          this.historialTratamientos = r.data || []
        })
        .finally(() => {
          this.loading = false
        })
    },

    agregarAcarritoTratamiento (tratamiento) {
      if (!tratamiento || !tratamiento.productos || !tratamiento.productos.length) {
        this.$alert.error('Este tratamiento no tiene medicamentos.')
        return
      }

      // marcar pagado true (como tenías)
      this.$axios.put(`tratamientos/pagado/${tratamiento.id}`, { pagado: true }).catch(() => {})

      this.carrito = []
      tratamiento.productos.forEach(med => {
        // const existing = this.carrito.find(p => p.nombre === med.medicamento)
        // if (existing) {
        //   existing.cantidadVenta = Number(existing.cantidadVenta) + Number(med.cantidad || 0)
        // } else {
        //   this.carrito.push({
        //     id: med.producto?.id || null,
        //     nombre: med.medicamento,
        //     cantidadVenta: parseInt(med.cantidad || 1),
        //     precioVenta: Number(med.precio || 0),
        //     tipo: 'Tratamiento'
        //   })
        // }
        this.carrito.push({
          id: med.producto?.id || null,
          nombre: med.producto?.nombre || 'Medicamento Desconocido',
          cantidadVenta: parseInt(med.cantidad || 1),
          precioVenta: Number(med.precio || 0),
          tipo: 'Tratamiento'
        })
      })

      // restaurar mascota del tratamiento
      this.mascota = {
        id: tratamiento.historial?.mascota?.id,
        nombre: tratamiento.historial?.mascota?.nombre,
        propietario_nombre: tratamiento.historial?.mascota?.propietario_nombre
      }

      this.$alert.success('Tratamiento agregado al carrito.')
      this.recuperarTratamientoDialog = false
      this.dialogVenta = true
    },

    togglePagado (item) {
      this.$axios.put(`tratamientos/pagado/${item.id}`, { pagado: item.pagado }).then(() => {
        this.$alert.success(`Tratamiento ${item.pagado ? 'marcado como pagado' : 'desmarcado como pagado'}.`)
      }).catch(() => {
        this.$alert.error('Error al actualizar el estado del tratamiento.')
        item.pagado = !item.pagado
      })
    }
  }
}
</script>
