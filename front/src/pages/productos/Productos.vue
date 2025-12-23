<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>

      <!-- FILTROS -->
      <q-card-section>
        <q-form @submit.prevent="productosGet">
          <div class="row q-col-gutter-sm items-center">

            <div class="col-12 col-md-3">
              <q-input v-model="filter" label="Buscar" outlined dense clearable>
                <template v-slot:prepend><q-icon name="search"/></template>
              </q-input>
            </div>

            <div class="col-12 col-md-2">
              <q-select v-model="filterTipo" label="Tipo" outlined dense :options="tipos" />
            </div>

            <div class="col-12 col-md-2">
              <q-btn color="primary" label="Buscar" type="submit" icon="search" no-caps :loading="loading"/>
            </div>

            <div class="col-12 col-md-5 text-right">
              <q-btn color="positive" label="Nuevo" icon="add_circle_outline" no-caps @click="productoNew"/>
            </div>

          </div>
        </q-form>

        <div class="flex flex-center q-mt-sm">
          <q-pagination
            v-if="totalPages > 1"
            v-model="currentPage"
            :max="totalPages"
            :max-pages="6"
            boundary-numbers
            @update:model-value="productosGet"
          />
        </div>
      </q-card-section>

      <!-- TABLA -->
      <q-markup-table dense wrap-cells flat bordered>
        <thead class="bg-black text-white">
        <tr>
          <th>Opciones</th>
          <th>Código</th>
          <th>Nombre</th>
          <th>Imagen</th>
          <th>Compra</th>
          <th>Venta</th>
          <th>Stock</th>
          <th>Tipo</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="p in productos" :key="p.id">
          <td>
            <q-btn-dropdown dense size="10px" label="Opciones" no-caps color="orange">
              <q-list>
                <q-item clickable @click="productoEdit(p)">
                  <q-item-section avatar><q-icon name="edit"/></q-item-section>
                  <q-item-section>Editar</q-item-section>
                </q-item>

                <q-item clickable @click="productoDelete(p.id)">
                  <q-item-section avatar><q-icon name="delete"/></q-item-section>
                  <q-item-section>Eliminar</q-item-section>
                </q-item>

                <q-item clickable @click="verImagen(p.imagen)">
                  <q-item-section avatar><q-icon name="image"/></q-item-section>
                  <q-item-section>Ver imagen</q-item-section>
                </q-item>

                <q-item clickable @click="cambiarImagen(p.id)">
                  <q-item-section avatar><q-icon name="photo_camera"/></q-item-section>
                  <q-item-section>Cambiar imagen</q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </td>

          <td>{{ p.codigo }}</td>
          <td>{{ p.nombre }}</td>
          <td>
            <q-img
              v-if="p.imagen"
              :src="`${$url}uploads/${p.imagen}`"
              style="width:35px;height:35px"
            />
          </td>
          <td>{{ p.precioCompra }}</td>
          <td>{{ p.precioVenta }}</td>
          <td>{{ p.stock }}</td>
          <td>
            <q-chip dense :color="getColor(p.tipo)">{{ p.tipo }}</q-chip>
          </td>
        </tr>
        </tbody>
      </q-markup-table>

    </q-card>

    <!-- DIALOG -->
    <q-dialog v-model="productoDialog" persistent position="right" maximized>
      <q-card style="min-width:350px">
        <q-card-section class="row items-center">
          <div class="text-bold">{{ actionProducto }} Producto</div>
          <q-space/>
          <q-btn icon="close" flat round dense @click="productoDialog=false"/>
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="producto.id ? productoPut() : productoPost()">
            <q-input v-model="producto.codigo" label="Código" outlined dense/>
            <q-input v-model="producto.nombre" label="Nombre" outlined dense/>
            <q-input v-model="producto.precioCompra" label="Precio Compra" type="number" outlined dense/>
            <q-input v-model="producto.precioVenta" label="Precio Venta" type="number" outlined dense/>
            <q-input v-model="producto.stock" label="Stock" type="number" outlined dense/>
            <q-select v-model="producto.tipo" label="Tipo" outlined dense :options="tipos"/>

            <div class="text-right q-mt-sm">
              <q-btn label="Cancelar" color="negative" @click="productoDialog=false" no-caps/>
              <q-btn label="Guardar" color="primary" type="submit" class="q-ml-sm" no-caps/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>
