<template>
  <div class="row">
    <app-loading v-if="loading"></app-loading>
    <div v-else class="col-md-12 animated fade-in">
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
        <paginate
          :page-count="dataPage"
          :click-handler="pageCallback"
          :prev-text="'Anterior'"
          :next-text="'Siguiente'"
          :container-class="'pagination'"
          :page-class="'page-item'"
          :page-link-class="'page-link'"
          :prev-class="'page-item'"
          :next-class="'page-item'"
          :prev-link-class="'page-link'"
          :next-link-class="'page-link'">
        </paginate>
    </div>
  </div>
</template>

<script>

  export default {
    data() {
      return {
        users: {},
        dataPage: 0,
        loading: true

      }
    },
    methods: {
      loadUsers(){
        axios.get("api/user").then(({ data }) => {
          this.dataPage = data.last_page;
          this.users = data.data;
          this.loading = false;
        });
      },
      pageCallback(pageNum){
        axios.get("/api/user?page=" + pageNum).then(({ data }) => (this.users = data.data));
      }
    },
    created() {
        this.loadUsers();
    }
  }
</script>
