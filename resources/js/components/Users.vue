<template>
  <div class="row">
    <div class="col-md-12">
          <table class="table thead-light table-hover">
            <tbody>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Fecha de creación</th>
              </tr>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.created_at }}</td>
            </tr>
          </tbody>
        </table>
    </div>
  </div>
</template>

<script>

  export default {
    data() {
      return {
        users: {}
      }
    },
    methods: {
      loadUsers(){
        axios.get("api/user").then(({ data }) => (this.users = data.data));
      }
    },
    created() {
        this.loadUsers();
    }
  }
</script>
