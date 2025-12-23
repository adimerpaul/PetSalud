<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>

      <!-- HEADER -->
      <q-card-section class="row items-center ">
        <q-avatar color="primary" text-color="white" icon="manage_accounts" class="q-mr-xs" />
        <div class="col">
          <div class="text-h6 text-weight-bold">Mi Cuenta</div>
          <div class="text-caption text-grey-7">
            Actualiza tus datos, tu avatar y tu contraseña.
          </div>
        </div>

        <div class="col-auto">
          <q-btn
            outline
            color="primary"
            icon="refresh"
            label="Actualizar"
            no-caps
            :loading="loading"
            @click="load"
            class="q-mr-sm"
          />
          <q-btn
            color="positive"
            icon="save"
            label="Guardar datos"
            no-caps
            :loading="savingProfile"
            @click="saveProfile"
          />
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div class="row q-col-gutter-lg">

          <!-- IZQ: DATOS -->
          <div class="col-12 col-md-7">
            <q-banner rounded class="bg-grey-1 text-grey-9 q-mb-md">
              <template v-slot:avatar>
                <q-icon name="info" />
              </template>
              Los cambios se aplican a tu usuario actual.
            </q-banner>

            <q-card flat bordered>
              <q-card-section class="q-gutter-md">
                <q-input
                  v-model="me.name"
                  outlined dense stack-label
                  label="Nombre"
                  :rules="[v => !!v || 'Requerido']"
                >
                  <template v-slot:prepend><q-icon name="badge" /></template>
                </q-input>

                <q-input
                  v-model="me.username"
                  outlined dense stack-label
                  label="Usuario"
                  :rules="[v => !!v || 'Requerido']"
                >
                  <template v-slot:prepend><q-icon name="person" /></template>
                </q-input>

                <q-input
                  v-model="me.email"
                  outlined dense stack-label
                  label="Email"
                  type="email"
                >
                  <template v-slot:prepend><q-icon name="mail" /></template>
                </q-input>
              </q-card-section>

              <q-separator />

              <!-- Rol / Veterinaria: mejor como tarjetas (cuadra perfecto) -->
              <q-card-section class="row q-col-gutter-md">
                <div class="col-12 col-md-6">
                  <q-card flat bordered class="bg-grey-1">
                    <q-item>
                      <q-item-section avatar>
                        <q-avatar color="primary" text-color="white" icon="admin_panel_settings" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label class="text-grey-7">Rol</q-item-label>
                        <q-item-label class="text-weight-medium">{{ me.role || '-' }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-card>
                </div>

                <div class="col-12 col-md-6">
                  <q-card flat bordered class="bg-grey-1">
                    <q-item>
                      <q-item-section avatar>
                        <q-avatar color="primary" text-color="white" icon="pets" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label class="text-grey-7">Veterinaria</q-item-label>
                        <q-item-label class="text-weight-medium">
                          {{ me.veterinaria?.nombre || '-' }}
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>

          <!-- DER: AVATAR -->
          <div class="col-12 col-md-5">
            <q-card flat bordered>
              <q-card-section class="row items-center">
                <q-icon name="image" size="22px" class="q-mr-sm" />
                <div class="text-subtitle1 text-weight-bold">Avatar</div>
                <q-space />
                <q-btn flat dense round icon="photo_camera" @click="$refs.avatarInput.click()" />
              </q-card-section>

              <q-separator />

              <q-card-section class="text-center">
                <q-avatar size="120px" rounded>
                  <q-img v-if="previewUrl" :src="previewUrl" fit="cover" />
                  <q-img v-else-if="me.avatar" :src="avatarUrl" fit="cover" />
                  <q-icon v-else name="person" size="56px" />
                </q-avatar>

                <div class="row justify-center q-gutter-sm q-mt-md">
                  <q-btn
                    outline
                    color="primary"
                    icon="photo"
                    label="Elegir foto"
                    no-caps
                    @click="$refs.avatarInput.click()"
                  />
                  <q-btn
                    v-if="selectedFile"
                    color="positive"
                    icon="cloud_upload"
                    label="Subir"
                    no-caps
                    :loading="uploading"
                    @click="uploadAvatar"
                  />
                </div>

                <input
                  ref="avatarInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="onPickFile"
                />

                <q-banner
                  v-if="selectedFile"
                  rounded
                  class="bg-grey-2 text-grey-8 q-mt-md"
                >
                  <template v-slot:avatar><q-icon name="upload_file" /></template>
                  Archivo listo: <b>{{ selectedFile.name }}</b>
                </q-banner>
              </q-card-section>
            </q-card>
          </div>

        </div>

        <q-separator class="q-my-lg" />

        <!-- CAMBIAR CONTRASEÑA -->
        <q-card flat bordered>

          <q-card-section>
            <q-form class="q-gutter-md" @submit.prevent="changePassword"   ref="passwordForm">
              <q-card-section class="row items-center ">
                <q-avatar color="primary" text-color="white" icon="lock_reset" class="q-mr-xs"/>
                <div class="col">
                  <div class="text-subtitle1 text-weight-bold">Cambiar contraseña</div>
                  <div class="text-caption text-grey-7">
                    Confirma tu contraseña actual para poder cambiarla.
                  </div>
                </div>
                <div class="col-auto">
                  <q-btn
                    color="positive"
                    icon="save"
                    label="Actualizar contraseña"
                    no-caps
                    :loading="savingPassword"
                    type="submit"
                  />
                </div>
              </q-card-section>

<!--              <q-separator />-->

              <q-input
                v-model="pass.current_password"
                outlined dense stack-label
                label="Contraseña actual"
                :type="show1 ? 'text' : 'password'"
                :rules="[v => !!v || 'Requerido']"
              >
                <template v-slot:prepend><q-icon name="lock" /></template>
                <template v-slot:append>
                  <q-btn flat round dense :icon="show1 ? 'visibility' : 'visibility_off'" @click="show1 = !show1" />
                </template>
              </q-input>

              <div class="row ">
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="pass.password"
                    outlined dense stack-label
                    label="Nueva contraseña"
                    :type="show2 ? 'text' : 'password'"
                    :rules="[
                      v => !!v || 'Requerido',
                      v => (v && v.length >= 6) || 'Mínimo 6 caracteres'
                    ]"
                  >
                    <template v-slot:prepend><q-icon name="enhanced_encryption" /></template>
                    <template v-slot:append>
                      <q-btn flat round dense :icon="show2 ? 'visibility' : 'visibility_off'" @click="show2 = !show2" />
                    </template>
                  </q-input>
                </div>

                <div class="col-12 col-md-6">
                  <q-input
                    v-model="pass.password_confirmation"
                    outlined dense stack-label
                    label="Confirmar nueva contraseña"
                    :type="show3 ? 'text' : 'password'"
                    :rules="[
                      v => !!v || 'Requerido',
                      v => v === pass.password || 'No coincide'
                    ]"
                  >
                    <template v-slot:prepend><q-icon name="verified" /></template>
                    <template v-slot:append>
                      <q-btn flat round dense :icon="show3 ? 'visibility' : 'visibility_off'" @click="show3 = !show3" />
                    </template>
                  </q-input>
                </div>
              </div>

              <q-banner rounded class="bg-grey-1 text-grey-9">
                <template v-slot:avatar><q-icon name="security" /></template>
                Recomendación: usa una contraseña distinta a las anteriores.
              </q-banner>

            </q-form>
          </q-card-section>
        </q-card>

      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
export default {
  name: 'MiCuenta',
  data () {
    return {
      loading: false,
      savingProfile: false,
      uploading: false,
      savingPassword: false,

      me: {},

      selectedFile: null,
      previewUrl: '',

      pass: {
        current_password: '',
        password: '',
        password_confirmation: ''
      },

      show1: false,
      show2: false,
      show3: false
    }
  },
  computed: {
    avatarUrl () {
      return this.me?.avatar
        ? `${this.$url}/../images/${this.me.avatar}`
        : ''
    }
  },
  mounted () {
    this.load()
  },
  methods: {
    load () {
      this.loading = true
      this.$axios.get('/me')
        .then(({ data }) => {
          this.me = data || {}
          this.$store.user = this.me
          localStorage.setItem('user', JSON.stringify(this.me))
        })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo cargar'))
        .finally(() => { this.loading = false })
    },

    saveProfile () {
      this.savingProfile = true
      const payload = {
        name: this.me.name,
        username: this.me.username,
        email: this.me.email
      }

      this.$axios.put('/me', payload)
        .then(({ data }) => {
          this.me = data || this.me
          this.$store.user = this.me
          localStorage.setItem('user', JSON.stringify(this.me))
          this.$alert.success('Datos actualizados')
        })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo guardar'))
        .finally(() => { this.savingProfile = false })
    },

    onPickFile (e) {
      const file = e.target.files?.[0]
      if (!file) return

      this.selectedFile = file
      if (this.previewUrl) URL.revokeObjectURL(this.previewUrl)
      this.previewUrl = URL.createObjectURL(file)
    },

    uploadAvatar () {
      if (!this.selectedFile) return

      this.uploading = true
      const fd = new FormData()
      fd.append('avatar', this.selectedFile)

      this.$axios.post('/me/avatar', fd, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
        .then(({ data }) => {
          const user = data?.user || null
          if (user) this.me = user
          else if (data?.avatar) this.me.avatar = data.avatar

          this.$store.user = this.me
          localStorage.setItem('user', JSON.stringify(this.me))

          this.selectedFile = null
          if (this.previewUrl) URL.revokeObjectURL(this.previewUrl)
          this.previewUrl = ''

          this.$alert.success('Avatar actualizado')
        })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo subir'))
        .finally(() => { this.uploading = false })
    },

    changePassword () {
      this.savingPassword = true
      this.$axios.put('/me/password', this.pass)
        .then(() => {
          this.$alert.success('Contraseña actualizada')
          this.pass.current_password = ''
          this.pass.password = ''
          this.pass.password_confirmation = ''
          this.$refs.passwordForm.reset()
          this.$refs.passwordForm.resetValidation()
        })
        .catch(e => this.$alert.error(e?.response?.data?.message || 'No se pudo actualizar'))
        .finally(() => { this.savingPassword = false })
    }
  },
  beforeUnmount () {
    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl)
  }
}
</script>
