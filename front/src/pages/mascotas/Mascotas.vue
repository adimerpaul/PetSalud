<template>
  <q-page class="q-pa-xs">
    <q-card flat bordered>
      <q-card-section>
        <q-form @submit.prevent="mascotasGet">
          <div class="row q-col-gutter-sm items-center">
            <div class="col-12 col-md-3">
              <q-input v-model="filter" label="Buscar" outlined dense clearable>
                <template v-slot:prepend><q-icon name="search"/></template>
              </q-input>
            </div>

            <div class="col-12 col-md-2">
              <q-btn color="primary" label="Buscar" type="submit" no-caps icon="search" :loading="loading" class="full-width"/>
            </div>

            <div class="col-12 col-md-7 text-right">
              <q-btn color="positive" label="Nuevo" no-caps icon="add_circle_outline" :loading="loading" to="/mascotas/create"/>
            </div>

            <div class="col-12 flex flex-center q-mt-sm">
              <q-pagination
                v-if="totalPages > 1"
                v-model="currentPage"
                :max="totalPages"
                :max-pages="6"
                boundary-numbers
                @update:model-value="mascotasGet"
              />
            </div>

            <div class="col-12 q-mt-sm">
              <q-markup-table dense wrap-cells flat bordered>
                <thead>
                <tr class="bg-black text-white">
<!--                  <th>Acciones</th>-->
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Especie</th>
                  <th>Raza</th>
                  <th>Sexo</th>
                  <th>Fecha Nac.</th>
                  <th>Señas</th>
                  <th>Color</th>
                  <th>Propietario</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="m in mascotas" :key="m.id" @click="goShow(m)">
<!--                  <td @click.stop>-->
<!--                    <q-btn-dropdown size="10px" color="primary" label="Acciones" no-caps dense>-->
<!--                      <q-list>-->
<!--                        <q-item clickable v-ripple @click="goShow(m)" v-close-popup>-->
<!--                          <q-item-section>-->
<!--                            <q-btn color="orange" dense size="10px" label="Ver" icon="visibility" no-caps />-->
<!--                          </q-item-section>-->
<!--                        </q-item>-->
<!--                        <q-item clickable v-ripple @click="goEdit(m)" v-close-popup>-->
<!--                          <q-item-section>-->
<!--                            <q-btn color="primary" dense size="10px" label="Editar" icon="edit" no-caps />-->
<!--                          </q-item-section>-->
<!--                        </q-item>-->
<!--                        <q-item clickable v-ripple @click="mascotaDelete(m)" v-close-popup>-->
<!--                          <q-item-section>-->
<!--                            <q-btn color="negative" dense size="10px" label="Eliminar" icon="delete" no-caps />-->
<!--                          </q-item-section>-->
<!--                        </q-item>-->
<!--                      </q-list>-->
<!--                    </q-btn-dropdown>-->
<!--                  </td>-->

                  <td>{{ m.id }}</td>
                  <td>{{ m.nombre }}</td>
                  <td>{{ m.especie }}</td>
                  <td>{{ m.raza }}</td>
                  <td>{{ m.sexo }}</td>
                  <td>{{ m.fecha_nac }}</td>
                  <td>{{ m.senas_particulares }}</td>
                  <td>{{ m.color }}</td>
                  <td>{{ m.propietario_nombre }}</td>
                </tr>
                </tbody>
              </q-markup-table>
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
export default {
  data () {
    return {
      filter: '',
      loading: false,
      mascotas: [],
      totalPages: 1,
      currentPage: 1
    }
  },
  created () { this.mascotasGet() },
  methods: {
    goShow (m) { this.$router.push(`/mascotas/${m.id}`) },
    goEdit (m) { this.$router.push(`/mascotas/${m.id}/edit`) },

    mascotaDelete (m) {
      this.$alert.confirm('¿Estás seguro de eliminar la mascota?')
        .onOk(() => {
          this.$axios.delete(`/mascotas/${m.id}`)
            .then(() => this.mascotasGet())
        })
    },

    mascotasGet () {
      this.loading = true
      this.$axios.get('/mascotas', {
        params: { filter: this.filter, page: this.currentPage, limit: 20 }
      })
        .then(({ data }) => {
          this.mascotas = data.data || []
          this.totalPages = data.last_page || 1
        })
        .finally(() => { this.loading = false })
    }
  }
}
</script>
