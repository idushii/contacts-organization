<template>
  <div class="row">
    <div class="col s12">
      <div class="card-panel">
        <div class="row">
          <div class="input-field col s10">
            <input placeholder="" id="Quest" type="text" v-model="config.quest" @keyup.enter="startSeach" autofocus :disabled="isSearch">
            <label for="Quest">Запрос</label>
          </div>
          <button class="waves-effect waves-light btn col s2" :class="{'disabled':isSearch}" @click="startSeach">Найти</button>
          <div class="col s12">
            <i class="fa" :class="{'fa-square-o': (!isSearchCompany & !completeLoadCompanys), 'fa-spinner': (isSearchCompany & !completeLoadCompanys), 'fa-check':  (!isSearchCompany & completeLoadCompanys)}" aria-hidden="true"></i>&nbsp;&nbsp;Загрузка списка организаций {{results.length != 0 ? results.length : ''}}
          </div>
          <div class="col s12">
            <i class="fa" :class="{'fa-square-o': (!isSearchEmails & !completeLoadEmails), 'fa-spinner': (isSearchEmails & !completeLoadEmails), 'fa-check':  (!isSearchEmails & completeLoadEmails)}" aria-hidden="true"></i>&nbsp;&nbsp;Загрузка email`ов {{`${countLoadsEmails} / ${companyHasSite.length}`}}
            <br>
          </div>
          <div class="col s12 tags" v-if="!isSearch">
            <div class="chip add-search" v-for="(cat, index) in this.categories" @click="selectCat(cat)" v-if="((index < 5 && !showAllCat) || showAllCat)">{{cat}}</div>
            <div class="chip add-search" v-if="!showAllCat & isSearch" @click="showAllCat = true">...</div>
            <div class="chip add-search" v-if="showAllCat" @click="showAllCat = false">Скрыть</div>
          </div>
        </div>
        <div class="center" v-if="results.length == 0 & fullComplete">
          <p>Ничего не найдено</p>
        </div>
        <div class="row" v-for="(company, index) in results" v-if="(results.length - index) < 10 & !isSearch" :key="company">
          <div class="col s1 truncate">{{index+1}}</div>
          <div class="col s3 truncate">{{company.name}}</div>
          <div class="col s2 truncate">{{company.phones}}</span></div>
          <div class="col s3 truncate">{{company.adres}}</div>
          <div class="col s2 truncate ">{{company.url}}</div>
          <div class="col s1 truncate">{{company.emails}}</div>
        </div>
      </div>
    </div>
    <div class="fixed-action-btn">
      <a class="btn-floating btn-large waves-effect waves-light red" @click="save" id="dlLink" download="data.csv">
        <i class="large material-icons">save</i>
      </a>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'Search',
    data() {
      return {
        isSearch: false,
        results: [],
        categories: [],
        maxResult: 500,
        countEmails: 0,
        showAllCat: false,
        isSearchCompany: false,
        completeLoadCompanys: false,
        isSearchEmails: false,
        completeLoadEmails: false,
        countLoadsEmails: 0,
        skip: 0,
        config: {
          apiKey: "",
          city: "",
          quest: "",
          count: 10,
          getEmail: ''
        }
      }
    },
    methods: {
      selectCat(cat) {
        this.config.quest = cat;
        this.results.length = 0;
        this.isSearch = false;
        this.startSeach()
      },
      startSeach() {
        this.isSearch = true;
        this.showAllCat = false;
        this.completeLoadEmails = false;
        this.results.length = 0;
        this.isSearchCompany = true;
        this.completeLoadCompanys = false;
        this.search()
      },
      search() {
        axios.get(`https://search-maps.yandex.ru/v1/?text=${this.config.city} ${this.config.quest}&type=biz&lang=ru_RU&results=${this.config.count == "all" ? this.maxResult : this.config.count}&apikey=${this.config.apiKey}&skip=${this.skip}`)
          .then(response => {
            let result = (response.data.features.map(company => {
              let CompanyMetaData = company.properties.CompanyMetaData;
              return {
                name: CompanyMetaData.name,
                url: CompanyMetaData.url,
                phones: CompanyMetaData.Phones ? CompanyMetaData.Phones.map(phone => phone.formatted).join(', ') : '',
                adres: CompanyMetaData.address,
                emails: ''
              }
            }))
            result.forEach(function(company) {
              this.results.push(company)
            }, this);

            console.log('ищу теги')
            try {
              this.categories = response.data.features
                .map(company => company.properties.CompanyMetaData.Categories ? company.properties.CompanyMetaData.Categories.map(cat => cat.name).join(';') : '')
                .join(';').split(';').sort()
                .filter((company, index, list) => list.indexOf(company) == index)
            } catch (err) {
              Materialize.toast('Ошибка определения тегов.')
            }

            if (result.length < this.maxResult) {
              this.isSearchCompany = false;
              this.completeLoadCompanys = true;
              this.skip = 0;
              //Materialize.toast(`Загрузка завершена. Найдено ${this.results.length} записей.`)
              this.loadEmails();
            } else {
              this.skip += this.maxResult;
              this.search()
            }
          })
          .catch(err => Materialize.toast(err))
      },
      loadEmails() {
        this.countLoadsEmails = 0;
        this.isSearchEmails = true;
        let axiosPromises = this.companyHasSite.map(
          (company, index) => axios({
            method: 'get',
            url: this.config.getEmail + company.url,
            crossDomain: true
          })
          .then(result => {
            company.emails = result.data.join(', ')
            this.countLoadsEmails++;
            console.log(`load email #${index} (${this.countLoadsEmails}/${this.companyHasSite.length})`)
            Promise.resolve(`${company.emails}`)
          })
          .catch(err => {
            Promise.resolve('Ошибка получения email`а: ' + err)
          })
        )

        Promise.all(axiosPromises)
          .then(result => {
            this.isSearchEmails = false;
            this.completeLoadEmails = true;
            this.isSearch = false;
          })
          .catch(err => {
            alert(err)
          })
      },
      save() {
        //alert('Данный функционал еще не разработан')
        var blob = new Blob(["\ufeff", this.resultCSV], {
          type: 'application/json'
        });
        dlLink.href = URL.createObjectURL(blob);
      }
    },
    mounted() {
      axios.get('static/config.json')
        .then((config) => {
          console.log('load config')
          this.config = config.data;
          this.$nextTick(() => {
            Materialize.updateTextFields()
          })
        })
        .catch(err => Materialize.toast(err, 10000))
    },
    computed: {
      resultCSV() {
        return `name; phone; adres; site; email\n${this.fullDataCompany.join('\n')}`
      },
      fullDataCompany() {
        return this.results.map(company => `${company.name || ''}; ${company.phones || ''}; ${company.adres || ''}; ${company.url || ''}; ${company.emails || ''} `)
      },
      companyHasSite() {
        return this.results.filter(company => company.url)
      },
      fullComplete() {
        return this.completeLoadCompanys & this.completeLoadEmails
      }

    }
  }
</script>


<style scoped>
  button.col {
    margin-top: 14px;
  }
  
  .add-search {
    cursor: pointer;
  }
  
  .tags {
    margin-top: 20px;
  }
</style>