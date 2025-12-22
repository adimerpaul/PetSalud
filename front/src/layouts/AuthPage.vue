<template>
  <q-layout class="ps-bg">
    <q-page-container>
      <q-page class="flex flex-center q-pa-md">

        <q-card class="ps-card shadow-12" bordered>
          <div class="row no-wrap">

            <!-- LEFT (FORM) -->
            <div class="col-12 col-md-5 ps-left q-pa-xl">
              <!-- logo -->
              <div class="row items-center q-mb-lg">
                <q-img src="logo.png" style="width:46px;height:46px" fit="contain" />
                <div class="q-ml-md">
                  <div class="text-h6 text-weight-bold">PetSalud</div>
                  <div class="text-caption text-grey-6">Sistema Veterinario</div>
                </div>
              </div>

              <div class="text-h6 text-weight-bold q-mb-xs">
                {{ mode === 'login' ? 'LOGIN' : 'REGISTRO' }}
              </div>
              <div class="text-caption text-grey-6 q-mb-lg">
                {{ mode === 'login'
                ? 'Accede a tu cuenta para continuar'
                : 'Crea tu cuenta en PetSalud' }}
              </div>

              <!-- LOGIN FORM -->
              <q-form v-if="mode === 'login'" @submit.prevent="doLogin">
                <q-input
                  v-model="login.username"
                  filled
                  dense
                  label="Usuario"
                  class="q-mb-md"
                >
                  <template v-slot:prepend>
                    <q-icon name="person" />
                  </template>
                </q-input>

                <q-input
                  v-model="login.password"
                  filled
                  dense
                  label="Contraseña"
                  :type="showPassword ? 'text' : 'password'"
                  class="q-mb-md"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'visibility' : 'visibility_off'"
                      class="cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>

                <q-btn
                  label="Ingresar"
                  type="submit"
                  unelevated
                  rounded
                  class="full-width bg-primary text-white q-mt-sm"
                  :loading="loading"
                />

                <div class="text-center q-mt-md">
                  <div class="text-caption text-grey-6">¿No tienes cuenta?</div>
                  <q-btn
                    flat
                    no-caps
                    class="text-primary"
                    label="Crear cuenta"
                    @click="mode = 'register'"
                  />
                </div>
              </q-form>

              <!-- REGISTER FORM -->
              <q-form v-else @submit.prevent="doRegister">
                <q-input
                  v-model="reg.veterinaria_nombre"
                  filled
                  dense
                  label="Veterinaria"
                  class="q-mb-sm"
                >
                  <template v-slot:prepend>
                    <q-icon name="pets" />
                  </template>
                </q-input>

                <q-input
                  v-model="reg.name"
                  filled
                  dense
                  label="Nombre"
                  class="q-mb-sm"
                >
                  <template v-slot:prepend>
                    <q-icon name="badge" />
                  </template>
                </q-input>

                <q-input
                  v-model="reg.username"
                  filled
                  dense
                  label="Usuario"
                  class="q-mb-sm"
                >
                  <template v-slot:prepend>
                    <q-icon name="person" />
                  </template>
                </q-input>

                <q-input
                  v-model="reg.password"
                  filled
                  dense
                  label="Contraseña"
                  :type="showPassword ? 'text' : 'password'"
                  class="q-mb-md"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'visibility' : 'visibility_off'"
                      class="cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>

                <q-btn
                  label="Crear cuenta"
                  type="submit"
                  unelevated
                  rounded
                  class="full-width bg-primary text-white"
                  :loading="loading"
                />

                <div class="text-center q-mt-md">
                  <div class="text-caption text-grey-6">¿Ya tienes cuenta?</div>
                  <q-btn
                    flat
                    no-caps
                    class="text-primary"
                    label="Iniciar sesión"
                    @click="mode = 'login'"
                  />
                </div>
              </q-form>
            </div>

            <!-- RIGHT (HERO) -->
            <div class="col-md-7 ps-right gt-sm">
              <div class="ps-hero-wrap">
                <div class="ps-hero-card">
                  <img class="ps-hero-img" src="hero-vet.png" alt="hero" />
                </div>
              </div>
            </div>

          </div>
        </q-card>

      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'AuthPetSalud',

  data () {
    return {
      mode: 'login',
      loading: false,
      showPassword: false,

      login: { username: '', password: '' },

      reg: {
        veterinaria_nombre: '',
        name: '',
        username: '',
        password: ''
      }
    }
  },

  methods: {
    saveSession (token, user) {
      this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
      localStorage.setItem('tokenPetSalud', token)
      localStorage.setItem('user', JSON.stringify(user))
      // isLogged
      this.$store.isLogged = true
      this.$store.user = user
      // $store.permissions = (user.permissions || []).map(p => p.name)
      this.$router.push('/')
    },

    doLogin () {
      this.loading = true
      this.$axios.post('/login', this.login)
        .then((res) => { this.saveSession(res.data.token, res.data.user) })
        .catch((e) => {
          console.error(e)
          this.$alert.error(e?.response?.data?.message || 'Error de autenticación', 'Error')
        })
        .finally(() => { this.loading = false })
    },

    doRegister () {
      this.loading = true
      this.$axios.post('/register', this.reg)
        .then(r => this.saveSession(r.data.token, r.data.user))
        .catch((e) => {
          this.$alert.error(e?.response?.data?.message || 'Error al registrar usuario', 'Error')
        })
        .finally(() => { this.loading = false })
    }
  }
}
</script>

<style scoped>
/* Fondo similar al figma (lila suave) */
.ps-bg{
  background: radial-gradient(circle at 20% 20%, #f0ecff 0%, #ecebff 35%, #e9e7ff 100%);
}

/* Card principal (grande + redondeado) */
.ps-card{
  width: min(980px, 96vw);
  border-radius: 22px;
  overflow: hidden;
}

/* Panel izquierdo blanco */
.ps-left{
  background: #fff;
}

/* Panel derecho morado (parecido al figma) */
.ps-right{
  background: linear-gradient(135deg, #6d63ff 0%, #4b3df5 100%);
  position: relative;
}

/* circulitos decorativos simples */
.ps-right::before{
  content:"";
  position:absolute;
  width:80px;height:80px;
  border-radius:50%;
  background: rgba(255,255,255,.18);
  top: 22px; left: 22px;
}
.ps-right::after{
  content:"";
  position:absolute;
  width:110px;height:110px;
  border-radius:50%;
  background: rgba(255,255,255,.14);
  bottom: 26px; right: 26px;
}

/* centro del hero */
.ps-hero-wrap{
  height: 100%;
  display:flex;
  align-items:center;
  justify-content:center;
  padding: 40px;
}
.ps-hero-card{
  width: 360px;
  height: 360px;
  border-radius: 24px;
  background: rgba(255,255,255,.16);
  border: 1px solid rgba(255,255,255,.22);
  display:flex;
  align-items:center;
  justify-content:center;
}
.ps-hero-img{
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
</style>
