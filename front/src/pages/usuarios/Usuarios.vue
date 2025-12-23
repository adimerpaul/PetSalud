<template>
  <q-page class="q-pa-md">
    <q-table
      :rows="users"
      :columns="columns"
      dense
      wrap-cells
      flat
      bordered
      :rows-per-page-options="[0]"
      title="Usuarios"
      :filter="filter"
    >
      <template v-slot:top-right>
        <q-btn
          color="positive"
          label="Nuevo"
          @click="userNew"
          no-caps
          icon="add_circle_outline"
          :loading="loading"
          class="q-mr-sm"
        />
        <q-btn
          color="primary"
          label="Actualizar"
          @click="usersGet"
          no-caps
          icon="refresh"
          :loading="loading"
          class="q-mr-sm"
        />
        <q-input v-model="filter" label="Buscar" dense outlined>
          <template v-slot:append>
            <q-icon name="search"/>
          </template>
        </q-input>
      </template>

      <!-- ACTIONS -->
      <template v-slot:body-cell-actions="props">
        <q-td :props="props" class="text-center">
          <q-btn-dropdown label="Opciones" no-caps size="10px" dense color="primary">
            <q-list>
              <q-item clickable @click="userEdit(props.row)" v-close-popup>
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section><q-item-label>Editar</q-item-label></q-item-section>
              </q-item>

              <q-item clickable @click="userEditPassword(props.row)" v-close-popup>
                <q-item-section avatar><q-icon name="lock_reset"/></q-item-section>
                <q-item-section><q-item-label>Cambiar contraseña</q-item-label></q-item-section>
              </q-item>

              <q-item clickable @click="cambiarAvatar(props.row)" v-close-popup>
                <q-item-section avatar><q-icon name="image"/></q-item-section>
                <q-item-section><q-item-label>Cambiar avatar</q-item-label></q-item-section>
              </q-item>

              <q-separator/>

              <q-item clickable @click="userDelete(props.row.id)" v-close-popup>
                <q-item-section avatar><q-icon name="delete"/></q-item-section>
                <q-item-section><q-item-label>Eliminar</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>

      <!-- ROLE -->
      <template v-slot:body-cell-role="props">
        <q-td :props="props">
          <q-chip
            :label="props.row.role"
            :color="roleColor(props.row.role)"
            text-color="white"
            dense
            size="13px"
          />
        </q-td>
      </template>

      <!-- AVATAR -->
      <template v-slot:body-cell-avatar="props">
        <q-td :props="props">
          <q-avatar rounded>
            <q-img
              :src="`${$url}/../images/${props.row.avatar}`"
              width="40px"
              height="40px"
              v-if="props.row.avatar"
            />
            <q-icon name="person" size="32px" v-else/>
          </q-avatar>
        </q-td>
      </template>
    </q-table>

    <!-- DIALOG NUEVO/EDITAR -->
    <q-dialog v-model="userDialog" persistent>
      <q-card style="width: 420px; max-width: 95vw;">
        <q-card-section class="q-pb-none row items-center">
          <div class="text-weight-bold">{{ actionUser }} usuario</div>
          <q-space/>
          <q-btn icon="close" flat round dense @click="userDialog = false"/>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="user.id ? userPut() : userPost()">
            <q-input
              v-model="user.name"
              label="Nombre"
              dense
              outlined
              :rules="[val => !!val || 'Campo requerido']"
              class="q-mb-sm"
            />
            <q-input
              v-model="user.username"
              label="Usuario"
              dense
              outlined
              :rules="[val => !!val || 'Campo requerido']"
              class="q-mb-sm"
            />
            <q-input
              v-model="user.email"
              label="Email (opcional)"
              dense
              outlined
              class="q-mb-sm"
            />

            <q-input
              v-model="user.password"
              label="Contraseña"
              dense
              outlined
              :rules="[val => !!val || 'Campo requerido', val => (val?.length>=6) || 'Mínimo 6 caracteres']"
              v-if="!user.id"
              class="q-mb-sm"
              :type="showPassword ? 'text' : 'password'"
            >
              <template v-slot:append>
                <q-icon
                  :name="showPassword ? 'visibility' : 'visibility_off'"
                  class="cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </q-input>

            <q-select
              v-model="user.role"
              label="Rol"
              dense
              outlined
              :options="roles"
              :rules="[val => !!val || 'Campo requerido']"
              class="q-mb-md"
            />

            <div class="text-right">
              <q-btn color="negative" label="Cancelar" @click="userDialog = false" no-caps :loading="loading"/>
              <q-btn color="primary" label="Guardar" type="submit" no-caps :loading="loading" class="q-ml-sm"/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- DIALOG AVATAR -->
    <q-dialog v-model="cambioAvatarDialogo" persistent>
      <q-card style="min-width: 420px; max-width: 95vw;">
        <q-card-section class="q-pb-none row items-center text-bold">
          Cambiar avatar
          <q-space/>
          <q-btn icon="close" flat round dense @click="cambioAvatarDialogo = false"/>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="submitAvatar">
            <div class="q-mb-md">
              <q-btn
                icon="edit"
                outline
                no-caps
                label="Elegir foto"
                @click="$refs.fileInput.click()"
              />
            </div>

            <div class="q-mb-md">
              <img
                :src="`${$url}/../images/${user.avatar}`"
                width="260"
                height="260"
                style="border-radius: 14px; object-fit: cover;"
                v-if="user.avatar"
              />
              <q-icon name="person" size="80px" v-else/>
              <input ref="fileInput" type="file" style="display:none" @change="onFileChange" accept="image/*" />
            </div>

            <div class="text-right">
              <q-btn color="negative" label="Cancelar" @click="cambioAvatarDialogo = false" no-caps :loading="loading"/>
              <q-btn color="primary" label="Guardar" type="submit" no-caps :loading="loading" class="q-ml-sm" :disable="!avatarFile"/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'UsuariosPage',
  data () {
    return {
      users: [],
      user: {},
      userDialog: false,
      cambioAvatarDialogo: false,
      loading: false,
      actionUser: '',
      filter: '',
      showPassword: false,
      avatarFile: null,

      roles: ['Vendedor', 'Veterinario'],

      columns: [
        { name: 'actions', label: 'Acciones', align: 'center' },
        { name: 'name', label: 'Nombre', align: 'left', field: 'name' },
        { name: 'username', label: 'Usuario', align: 'left', field: 'username' },
        { name: 'avatar', label: 'Avatar', align: 'left', field: row => row.avatar },
        { name: 'role', label: 'Rol', align: 'left', field: 'role' },
        { name: 'email', label: 'Email', align: 'left', field: 'email' }
      ]
    }
  },

  mounted () {
    this.usersGet()
  },

  methods: {
    roleColor (role) {
      if (role === 'Administrador') return 'purple'
      if (role === 'Veterinario') return 'teal'
      return 'blue'
    },

    usersGet () {
      this.loading = true
      this.$axios.get('users')
        .then(res => { this.users = res.data })
        .catch(err => this.$alert.error(err?.response?.data?.message || 'Error cargando usuarios'))
        .finally(() => { this.loading = false })
    },

    userNew () {
      this.user = {
        name: '',
        email: '',
        password: '',
        username: '',
        role: 'Vendedor'
      }
      this.actionUser = 'Nuevo'
      this.showPassword = false
      this.userDialog = true
    },

    userEdit (u) {
      this.user = { ...u }
      this.actionUser = 'Editar'
      this.userDialog = true
    },

    userPost () {
      this.loading = true
      this.$axios.post('users', this.user)
        .then(() => {
          this.userDialog = false
          this.$alert.success('Usuario creado')
          this.usersGet()
        })
        .catch(err => this.$alert.error(err?.response?.data?.message || 'Error al crear'))
        .finally(() => { this.loading = false })
    },

    userPut () {
      this.loading = true
      const payload = { ...this.user }
      delete payload.password // por seguridad, password no se actualiza aquí
      this.$axios.put('users/' + this.user.id, payload)
        .then(() => {
          this.userDialog = false
          this.$alert.success('Usuario actualizado')
          this.usersGet()
        })
        .catch(err => this.$alert.error(err?.response?.data?.message || 'Error al actualizar'))
        .finally(() => { this.loading = false })
    },

    userEditPassword (u) {
      this.$alert.dialogPrompt('Nueva contraseña', 'Ingrese la nueva contraseña', 'password')
        .onOk(password => {
          this.loading = true
          this.$axios.put(`users/${u.id}/password`, { password })
            .then(() => {
              this.$alert.success('Contraseña actualizada')
            })
            .catch(err => this.$alert.error(err?.response?.data?.message || 'Error al cambiar contraseña'))
            .finally(() => { this.loading = false })
        })
    },

    userDelete (id) {
      this.$alert.dialog('¿Desea eliminar el usuario?')
        .onOk(() => {
          this.loading = true
          this.$axios.delete('users/' + id)
            .then(() => {
              this.$alert.success('Usuario eliminado')
              this.usersGet()
            })
            .catch(err => this.$alert.error(err?.response?.data?.message || 'Error al eliminar'))
            .finally(() => { this.loading = false })
        })
    },

    cambiarAvatar (u) {
      this.user = { ...u }
      this.avatarFile = null
      this.cambioAvatarDialogo = true
    },

    onFileChange (event) {
      this.avatarFile = event.target.files[0] || null
    },

    submitAvatar () {
      if (!this.avatarFile) return
      const formData = new FormData()
      formData.append('avatar', this.avatarFile)

      this.loading = true
      this.$axios.post(`users/${this.user.id}/avatar`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
        .then(res => {
          this.$alert.success('Avatar actualizado')
          this.cambioAvatarDialogo = false
          // refrescar tabla
          this.usersGet()
          // si cambió su propio avatar, también refrescar store (opcional)
          if (this.$store.user?.id === this.user.id) {
            this.$store.user.avatar = res.data.avatar
          }
        })
        .catch(err => this.$alert.error(err?.response?.data?.message || 'Error subiendo avatar'))
        .finally(() => { this.loading = false })
    }
  }
}
</script>
