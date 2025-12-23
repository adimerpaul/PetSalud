<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>

      <!-- HEADER -->
      <q-card-section class="row items-center">
        <q-avatar icon="inventory" color="primary" text-color="white"/>
        <div class="q-ml-md">
          <div class="text-h6 text-weight-bold">Productos</div>
          <div class="text-caption text-grey-7">Gestión de productos y tratamientos</div>
        </div>
        <q-space/>
        <q-btn color="positive" icon="add" label="Nuevo" no-caps @click="nuevo"/>
      </q-card-section>

      <q-separator/>

      <!-- FILTROS -->
      <q-card-section>
        <div class="row q-col-gutter-sm">
          <div class="col-12 col-md-4">
            <q-input v-model="filter" label="Buscar" outlined dense clearable>
              <template v-slot:prepend><q-icon name="search"/></template>
            </q-input>
          </div>

          <div class="col-12 col-md-3">
            <q-select v-model="filterTipo" label="Tipo" outlined dense :options="tipos" clearable/>
          </div>

          <div class="col-12 col-md-2">
            <q-btn color="primary" label="Buscar" icon="search" no-caps @click="get"/>
          </div>
        </div>
      </q-card-section>

      <!-- TABLA -->
      <q-markup-table dense flat bordered>
        <thead class="bg-black text-white">
        <tr>
          <th>Acciones</th>
          <th>Imagen</th>
          <th>Código</th>
          <th>Nombre</th>
          <th>Compra</th>
          <th>Venta</th>
          <th>Stock</th>
          <th>Tipo</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="p in productos" :key="p.id">
          <td>
            <q-btn-dropdown dense size="10px" color="orange" label="Opciones">
              <q-list>
                <q-item clickable @click="editar(p)">
                  <q-item-section avatar><q-icon name="edit"/></q-item-section>
                  <q-item-section>Editar</q-item-section>
                </q-item>

                <q-item clickable @click="eliminar(p.id)">
                  <q-item-section avatar><q-icon name="delete"/></q-item-section>
                  <q-item-section>Eliminar</q-item-section>
                </q-item>

                <q-item clickable @click="cambiarImagen(p.id)">
                  <q-item-section avatar><q-icon name="photo_camera"/></q-item-section>
                  <q-item-section>Cambiar imagen</q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </td>

          <td>
            <q-img
              :src="`${$url}/../uploads/${p.imagen}`"
              style="width:35px;height:35px"
              fit="cover"
            />
          </td>

          <td>{{ p.codigo }}</td>
          <td>{{ p.nombre }}</td>
          <td>{{ p.precioCompra }}</td>
          <td>{{ p.precioVenta }}</td>
          <td>{{ p.stock }}</td>
          <td>
            <q-chip dense :color="colorTipo(p.tipo)">
              {{ p.tipo }}
            </q-chip>
          </td>
        </tr>
        </tbody>
      </q-markup-table>

      <div class="flex flex-center q-pa-sm">
        <q-pagination
          v-if="totalPages>1"
          v-model="page"
          :max="totalPages"
          @update:model-value="get"
        />
      </div>
    </q-card>

    <!-- DIALOG -->
    <q-dialog v-model="dialog" position="right" maximized>
      <q-card style="min-width:350px">
        <q-card-section class="row items-center">
          <div class="text-bold">{{ producto.id ? 'Editar' : 'Nuevo' }} Producto</div>
          <q-space/>
          <q-btn icon="close" flat round dense @click="dialog=false"/>
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="guardar">
            <q-input v-model="producto.codigo" label="Código" outlined dense/>
            <q-input v-model="producto.nombre" label="Nombre" outlined dense/>
            <q-input v-model="producto.precioCompra" label="Precio Compra" type="number" outlined dense/>
            <q-input v-model="producto.precioVenta" label="Precio Venta" type="number" outlined dense/>
            <q-input v-model="producto.stock" label="Stock" type="number" outlined dense/>
            <q-select v-model="producto.tipo" label="Tipo" outlined dense :options="tipos"/>

            <div class="text-right q-mt-sm">
              <q-btn label="Cancelar" color="negative" flat @click="dialog=false"/>
              <q-btn label="Guardar" color="primary" type="submit" class="q-ml-sm"/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data () {
    return {
      productos: [],
      producto: {},
      dialog: false,
      loading: false,
      filter: '',
      filterTipo: '',
      page: 1,
      totalPages: 1,
      tipos: ['Producto','Tratamiento','Laboratorio','Cirugía','Peluqueria']
    }
  },
  mounted () { this.get() },
  methods: {
    get () {
      this.$axios.get('productos',{
        params:{
          filter:this.filter,
          tipo:this.filterTipo,
          page:this.page,
          limit:20
        }
      }).then(r=>{
        this.productos = r.data.data
        this.totalPages = r.data.last_page
      })
    },
    nuevo () {
      this.producto = { tipo:'Producto', stock:1 }
      this.dialog = true
    },
    editar (p) {
      this.producto = { ...p }
      this.dialog = true
    },
    guardar () {
      const req = this.producto.id
        ? this.$axios.put(`productos/${this.producto.id}`,this.producto)
        : this.$axios.post('productos',this.producto)

      req.then(()=>{
        this.dialog=false
        this.get()
        this.$alert.success('Guardado correctamente')
      })
    },
    eliminar (id) {
      this.$alert.confirm('¿Eliminar producto?')
        .onOk(()=>this.$axios.delete(`productos/${id}`).then(this.get))
    },
    cambiarImagen (id) {
      const i = document.createElement('input')
      i.type='file'
      i.accept='image/*'
      i.onchange=()=>{
        const fd = new FormData()
        fd.append('photo',i.files[0])
        this.$axios.post(`productos/${id}/imagen`,fd).then(this.get)
      }
      i.click()
    },
    colorTipo (t) {
      return {
        Producto:'orange',
        Tratamiento:'purple',
        Laboratorio:'blue',
        Cirugía:'red',
        Peluqueria:'green'
      }[t] || 'grey'
    }
  }
}
</script>
