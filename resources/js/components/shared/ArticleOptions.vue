<template>
  <div class="article-options">
    <button @click="createModal()" type="button" class="btn btn-outline-success btn-sm mb-4 mr-2">
      <i class="fa fa-plus mr-1"></i>
      Crear artículo
    </button>
    <button v-if="articles.length !== 0" @click="randomArticle()" type="button" class="btn btn-outline-primary btn-sm mb-4">
      <i class="fa fa-random mr-1"></i>
      Artículo aleatorio
    </button>
    <div class="modal fade" id="createArticleModal" tabindex="-1" role="dialog" aria-labelledby="createArticleModal" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="editMode ? updateArticle() : createArticle()">
            <div class="modal-header">
              <h5 v-if="!editMode" class="modal-title">Nuevo artículo</h5>
              <h5 v-else class="modal-title">Editar artículo</h5>
              <button v-if="!editMode" type="button" class="btn btn-outline-success btn-sm ml-auto" :disabled="generating" @click="generateArticle();">
                <i :class="{ 'fa-spin' : generating }" class="fa fa-refresh mr-1"></i>{{ msgGenerate }}
              </button>
              <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label for="title">Título</label>
                  <input v-model="form.title" type="text" class="form-control" :class="{'is-invalid': form.errors.has('title')}" id="title" placeholder="Ingresa un título..." maxlength="191">
                  <has-error :form="form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <label for="image">Imagen</label>
                  <input v-model="form.image" type="text" class="form-control" :class="{'is-invalid': form.errors.has('image')}" id="image" placeholder="Ingresa una imagen..." maxlength="191">
                  <has-error :form="form" field="image"></has-error>
                </div>
                <div class="form-group">
                  <label for="body">Contenido</label>
                  <textarea v-model="form.body" class="form-control" :class="{'is-invalid': form.errors.has('body')}" id="body" placeholder="Ingresa un contenido..." rows="5"></textarea>
                  <has-error :form="form" field="body"></has-error>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button v-if="!editMode" :disabled="form.busy" type="submit" class="btn btn-primary">Guardar</button>
              <button v-else :disabled="form.busy" type="submit" class="btn btn-primary">Actualizar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { Form, HasError, AlertError } from 'vform';
  Vue.component(HasError.name, HasError);
  Vue.component(AlertError.name, AlertError);

  export default {
    data() {
      return {
        editMode: false,
        msgGenerate: 'Generar artículo',
        generating: false,
        sumViews : 0,
        randomArticleId: 0,
        query: {}
      }
    },
    props: {
      articles: {
        value: [Array, Object]
      },
      form: {
        type: Object,
        default: () => (
          new Form ({
            id: '',
            title: '',
            image: '',
            body: '',
            pageviews: '',
            source: ''
          })
        )
      },
    },
    methods: {
      generateArticle() {
        this.generating = true;
        this.msgGenerate = 'Generando...';
        axios.get("https://cors-anywhere.herokuapp.com/es.wikipedia.org/w/api.php?format=json&action=query&generator=random&grnnamespace=0&prop=extracts|pageimages|pageviews|extlinks&exchars=1000&pithumbsize=550", {
            headers: {"X-Requested-With": "XMLHttpRequest"}
          }).then(({ data }) => {
            for (let pageId in data.query.pages) {
              if (data.query.pages.hasOwnProperty(pageId) && data.query.pages[pageId].thumbnail.source) {
                  this.form.title = data.query.pages[pageId].title;
                  this.form.image = data.query.pages[pageId].thumbnail.source;
                  this.form.body = this.stripTags(data.query.pages[pageId].extract).replace(/\[.*?\]/g, '');
                  for (let totalViews in data.query.pages[pageId].pageviews) {
                    this.sumViews = this.sumViews + data.query.pages[pageId].pageviews[totalViews];
                  }
                  this.form.pageviews = this.sumViews;
                  this.sumViews = 0;
                  this.form.source = Object.values(data.query.pages[pageId].extlinks[0])[0];
                  this.msgGenerate = 'Generar artículo';
                  this.generating = false;
              }
            }
          })
          .catch(() => {
            this.generateArticle();
          });
      },
      createModal() {
        this.editMode = false,
        this.form.reset();
        $('#createArticleModal').modal('show');
      },
      editModal(article) {
        this.dataId = article.id;
        this.editMode = true,
        this.form.reset();
        $('#createArticleModal').modal('show');
        this.form.fill(article);
      },
      createArticle() {
        this.$Progress.start();
        this.form.post("/api/article").then(() => {
          this.$Progress.finish();
          this.$root.$emit('Articles');
          $("#createArticleModal .close").click();
          toast.fire({
            type: 'success',
            title: '¡Artículo creado exitosamente!'
          });
        })
        .catch(({ error }) => {
          this.$Progress.fail();
        });
      },
      updateArticle() {
        this.$Progress.start();
        this.form.put("api/article/" + this.form.id).then(() => {
          this.$Progress.finish();
          this.$root.$emit('Articles');
          $("#createArticleModal .close").click();
          toast.fire({
            type: 'success',
            title: '¡Artículo actualizado exitosamente!'
          });
        })
        .catch(({ error }) => {
          this.$Progress.fail();
        });
      },
      randomArticle() {
        axios.get("/api/random-article-id").then(({ data }) => {
          this.randomArticleId = data;
          this.$router.push("/articles/" + this.randomArticleId);
          this.$root.$emit('FullArticle');
        });
      },
      stripTags(html) {
        let tmp = document.createElement("div");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
      }
    },
    mounted() {
      this.$root.$on('ArticlesOptions', (article) => {
        this.editModal(article);
      });
    }
  }
</script>
