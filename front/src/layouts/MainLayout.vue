<template>
  <q-layout view="lHh Lpr lFf">
    <!-- HEADER -->
    <q-header class="bg-white text-black" bordered>
      <q-toolbar>
        <q-btn
          flat
          color="primary"
          :icon="leftDrawerOpen ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right'"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          dense
        />

        <div class="row items-center q-gutter-sm">
          <div class="text-subtitle1 text-weight-medium" style="line-height: 0.95">
            PetSalud · Panel de Gestión
          </div>
        </div>

        <q-space />

        <!-- USER DROPDOWN -->
        <q-btn-dropdown flat no-caps dropdown-icon="expand_more">
          <template v-slot:label>
            <div class="row items-center no-wrap q-gutter-sm">
              <q-avatar rounded>
                <q-img
                  :src="`${$url}/../images/${$store.user.avatar}`"
                  width="40px"
                  height="40px"
                  v-if="$store.user.avatar"
                />
                <q-icon name="person" v-else />
              </q-avatar>

              <div class="text-left" style="line-height: 1">
                <div class="ellipsis" style="max-width: 150px;">
                  {{ $store.user.username }}
                </div>
                <div class="text-caption text-grey-6">
                  {{ $store.user.role || 'Usuario' }}
                </div>
              </div>
            </div>
          </template>

          <!-- PERFIL -->
          <q-item clickable v-close-popup to="/mi-cuenta">
            <q-item-section avatar>
              <q-icon name="manage_accounts" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Mi cuenta</q-item-label>
              <q-item-label caption>Perfil, avatar y datos</q-item-label>
            </q-item-section>
          </q-item>

          <!-- CONTRASEÑA -->
<!--          <q-item clickable v-close-popup to="/mi-cuenta/password">-->
<!--            <q-item-section avatar>-->
<!--              <q-icon name="lock_reset" />-->
<!--            </q-item-section>-->
<!--            <q-item-section>-->
<!--              <q-item-label>Cambiar contraseña</q-item-label>-->
<!--              <q-item-label caption>Actualiza tu clave</q-item-label>-->
<!--            </q-item-section>-->
<!--          </q-item>-->

          <q-separator />

          <!-- PERMISOS -->
<!--          <q-item clickable v-close-popup>-->
<!--            <q-item-section>-->
<!--              <q-item-label class="text-grey-7">Permisos asignados</q-item-label>-->
<!--              <q-item-label caption class="q-mt-xs">-->
<!--                <div class="row q-col-gutter-xs" style="min-width: 180px; max-width: 220px;">-->
<!--                  <q-chip-->
<!--                    v-for="(p, i) in $store.permissions"-->
<!--                    :key="i"-->
<!--                    dense-->
<!--                    color="grey-3"-->
<!--                    text-color="black"-->
<!--                    size="12px"-->
<!--                    class="q-mr-xs q-mb-xs"-->
<!--                  >-->
<!--                    {{ p }}-->
<!--                  </q-chip>-->
<!--                  <q-badge v-if="!$store.permissions?.length" color="grey-5" outline>-->
<!--                    Sin permisos-->
<!--                  </q-badge>-->
<!--                </div>-->
<!--              </q-item-label>-->
<!--            </q-item-section>-->
<!--          </q-item>-->

          <q-separator />

          <!-- LOGOUT -->
          <q-item clickable v-ripple @click="logout" v-close-popup>
            <q-item-section avatar>
              <q-icon name="logout" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Salir</q-item-label>
            </q-item-section>
          </q-item>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <!-- DRAWER -->
    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      show-if-above
      :width="240"
      :breakpoint="500"
      class="bg-primary text-white"
    >
      <q-list class="q-pb-none">
        <!-- BRAND -->
        <q-item-label header class="text-center q-pa-none q-pt-md">
          <q-avatar size="64px" class="q-mb-sm bg-white" rounded>
<!--            <q-img src="/logo.png" width="64px" height="64px" fit="contain" />-->
            <q-img :src="`${$url}/../storage/veterinarias/${ $store.user?.veterinaria?.logo }`" width="64px" height="64px" fit="contain"
                   v-if="$store.user?.veterinaria?.logo"
            />
          </q-avatar>
          <div class="text-weight-bold text-white">PetSalud</div>
          <div class="text-caption text-white">Sistema Veterinario</div>
<!--          <pre>{{$store.user}}</pre>-->
        </q-item-label>

        <q-separator color="white" inset class="q-my-sm" />

        <!-- ====== PRINCIPAL ====== -->
        <q-item dense>
          <q-item-section>
            <q-item-label class="text-white">
              Opciones Principales
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item dense to="/" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="dashboard" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Principal</q-item-label>
          </q-item-section>
        </q-item>
<!--        usuarios datos empresa mi cuenta-->
        <q-item dense to="/usuarios" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="people" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Usuarios</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/datos-empresa" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="business" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Datos Empresa</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/mi-cuenta" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="manage_accounts" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Mi Cuenta</q-item-label>
          </q-item-section>
        </q-item>
<!--        prodcutos-->
        <q-item dense to="/productos" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="inventory_2" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Productos</q-item-label>
          </q-item-section>
        </q-item>
<!--        mascotas-->
        <q-item dense to="/mascotas" exact clickable class="menu-item" active-class="menu-active" v-close-popup >
          <q-item-section avatar>
            <q-icon name="pets" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Mascotas</q-item-label>
          </q-item-section>
        </q-item>
        <div class="q-pa-md">
          <div class="text-grey-3 text-caption">
            PetSalud v{{ $version }}
          </div>
          <div class="text-grey-3 text-caption">
            © {{ new Date().getFullYear() }} PetSalud
          </div>
        </div>

        <!-- SALIR (también en drawer) -->
        <q-item clickable class="text-white" @click="logout" v-close-popup>
          <q-item-section avatar>
            <q-icon name="logout" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Salir</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- PAGE -->
    <q-page-container class="bg-grey-2">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { getCurrentInstance, ref } from 'vue'

const { proxy } = getCurrentInstance()
const leftDrawerOpen = ref(false)

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function canPermission (permission) {
  return (proxy.$store.permissions || []).includes(permission)
}

function logout () {
  proxy.$alert.dialog('¿Desea salir del sistema?')
    .onOk(() => {
      proxy.$axios.post('/logout')
        .then(() => {
          proxy.$store.isLogged = false
          proxy.$store.user = {}
          proxy.$store.permissions = []
          localStorage.removeItem('tokenPetSalud')
          proxy.$router.push('/login')
        })
        .catch(() => proxy.$alert.error('Error al cerrar sesión. Intente nuevamente.'))
    })
}
</script>

<style scoped>
.menu-item {
  border-radius: 10px;
  margin: 4px 8px;
  padding: 6px 8px;
}
.menu-active {
  background: rgba(255, 255, 255, 0.15);
  color: #fff !important;
  border-radius: 10px;
}
</style>
