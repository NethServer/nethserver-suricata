<template>
  <div>
    <h2>{{ $t('bypass.title') }}</h2>

    <div v-show="!view.isLoaded" class="spinner spinner-lg"></div>

    <ul v-if="view.isLoaded" class="nav nav-tabs nav-tabs-pf">
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#source-tab"
          id="source-tab-parent"
        >{{$t('bypass.source')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#destination-tab"
          id="destination-tab-parent"
        >{{$t('bypass.destination')}}</a>
      </li>
    </ul>

    <div v-if=" view.isLoaded" class="tab-content">
      <div class="tab-pane fade active" id="source-tab" role="tabpanel" aria-labelledby="hosts-tab">
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateSource()"
          class="btn btn-primary btn-lg"
        >{{$t('bypass.add_source')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="sourceColumns"
          :rows="sourceRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span class="fa fa-user span-right-icon"></span>
              <a
                :class="['mg-left-5', props.row.props.status == 'enabled' ? '' : 'gray']"
                @click="props.row.props.status == 'enabled' ? openEditSource(props.row) : undefined"
              >
                <strong>{{ props.row.props.Host.name}}</strong>
                <span class="gray mg-left-5">({{ props.row.props.Host.type}})</span>
              </a>
            </td>
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span>{{props.row.props.Description}}</span>
            </td>
            <td>
              <button
                @click="props.row.props.status == 'disabled' ? toggleSourceStatus(props.row) : openEditSource(props.row)"
                :class="['btn btn-default', props.row.props.status == 'disabled' ? 'btn-primary' : '']"
              >
                <span
                  :class="['fa', props.row.props.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
                ></span>
                {{props.row.props.status == 'disabled' ? $t('enable') : $t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  id="dropdownKebabRight9"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="toggleSourceStatus(props.row)">
                      <span
                        :class="['fa', props.row.props.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                      ></span>
                      {{props.row.props.status == 'enabled' ? $t('disable') : $t('enable')}}
                    </a>
                  </li>
                  <li role="presentation" class="divider"></li>
                  <li>
                    <a @click="openDeleteSource(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>

      <div
        class="tab-pane fade active"
        id="destination-tab"
        role="tabpanel"
        aria-labelledby="hosts-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateDestination()"
          class="btn btn-primary btn-lg"
        >{{$t('bypass.add_destination')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="destinationColumns"
          :rows="destinationRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span class="fa fa-user span-right-icon"></span>
              <a
                :class="['mg-left-5', props.row.props.status == 'enabled' ? '' : 'gray']"
                @click="props.row.props.status == 'enabled' ? openEditDestination(props.row) : undefined"
              >
                <strong>{{ props.row.props.Host.name}}</strong>
                <span class="gray mg-left-5">({{ props.row.props.Host.type}})</span>
              </a>
            </td>
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span>{{props.row.props.Description}}</span>
            </td>
            <td>
              <button
                @click="props.row.props.status == 'disabled' ? toggleDestinationStatus(props.row) : openEditDestination(props.row)"
                :class="['btn btn-default', props.row.props.status == 'disabled' ? 'btn-primary' : '']"
              >
                <span
                  :class="['fa', props.row.props.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
                ></span>
                {{props.row.props.status == 'disabled' ? $t('enable') : $t('edit')}}
              </button>
              <div class="dropup pull-right dropdown-kebab-pf">
                <button
                  class="btn btn-link dropdown-toggle"
                  type="button"
                  id="dropdownKebabRight9"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="true"
                >
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a @click="toggleDestinationStatus(props.row)">
                      <span
                        :class="['fa', props.row.props.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                      ></span>
                      {{props.row.props.status == 'enabled' ? $t('disable') : $t('enable')}}
                    </a>
                  </li>
                  <li role="presentation" class="divider"></li>
                  <li>
                    <a @click="openDeleteDestination(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
    </div>
    <!-- MODALS -->
    <div class="modal" id="createSourceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentSource.isEdit ? $t('bypass.edit_source') + ' '+ currentSource.name : $t('bypass.add_source')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveSource(currentSource)">
            <div class="modal-body">
              <div :class="['form-group', currentSource.errors.Source.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.source')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentSource.Source"
                    :options="autoOptions"
                    :onInputChange="filterSrcAuto"
                    :onItemSelected="selectSrcAuto"
                    :required="!currentSource.isEdit"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
                        <span
                          v-show="props.item.IpAddress || props.item.Address"
                          class="gray"
                        >({{ props.item.IpAddress || props.item.Address }})</span>
                        <i class="mg-left-5">{{props.item.Description}}</i>
                        <b class="mg-left-5">{{props.item.type | capitalize}}</b>
                      </span>
                    </div>
                  </suggestions>
                  <span
                    v-if="currentSource.SrcType && currentSource.SrcType.length > 0"
                    class="help-block gray"
                  >{{currentSource.SrcType}}</span>
                  <span v-if="currentSource.errors.Source.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentSource.errors.Source.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', currentSource.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentSource.Description" class="form-control" />
                  <span
                    v-if="currentSource.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentSource.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentSource.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentSource.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="createDestinationModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentDestination.isEdit ? $t('bypass.edit_destination') + ' '+ currentDestination.name : $t('bypass.add_destination')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveDestination(currentDestination)">
            <div class="modal-body">
              <div
                :class="['form-group', currentDestination.errors.Destination.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.destination')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentDestination.Destination"
                    :options="autoOptions"
                    :onInputChange="filterDstAuto"
                    :onItemSelected="selectDstAuto"
                    :required="!currentDestination.isEdit"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
                        <span
                          v-show="props.item.IpAddress || props.item.Address"
                          class="gray"
                        >({{ props.item.IpAddress || props.item.Address }})</span>
                        <i class="mg-left-5">{{props.item.Description}}</i>
                        <b class="mg-left-5">{{props.item.type | capitalize}}</b>
                      </span>
                    </div>
                  </suggestions>
                  <span
                    v-if="currentDestination.DstType && currentDestination.DstType.length > 0"
                    class="help-block gray"
                  >{{currentDestination.DstType}}</span>
                  <span v-if="currentDestination.errors.Destination.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentDestination.errors.Destination.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', currentDestination.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentDestination.Description" class="form-control" />
                  <span
                    v-if="currentDestination.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentDestination.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div
                v-if="currentDestination.isLoading"
                class="spinner spinner-sm form-spinner-loader"
              ></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentDestination.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteSourceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('bypass.delete_source')}} {{toDeleteSource.props.Host.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteSource(toDeleteSource)">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="deleteDestinationModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('bypass.delete_destination')}} {{toDeleteDestination.props.Host.name}}</h4>
          </div>
          <form
            class="form-horizontal"
            v-on:submit.prevent="deleteDestination(toDeleteDestination)"
          >
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END MODALS -->
  </div>
</template>

<script>
export default {
  name: "Bypass",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  mounted() {
    this.getBypasses();
    this.getObjects();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      autoOptions: {
        inputClass: "form-control"
      },
      tableLangsTexts: this.tableLangs(),
      sourceColumns: [
        {
          label: this.$i18n.t("bypass.source"),
          field: "props.Host.name",
          filterable: true
        },
        {
          label: this.$i18n.t("bypass.description"),
          field: "props.Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      sourceRows: [],
      destinationColumns: [
        {
          label: this.$i18n.t("bypass.destination"),
          field: "props.Host.name",
          filterable: true
        },
        {
          label: this.$i18n.t("bypass.description"),
          field: "props.Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      destinationRows: [],
      currentSource: this.initSource(),
      currentDestination: this.initDestination(),
      toDeleteSource: {
        props: {
          Host: {
            name: ""
          },
          Domains: []
        }
      },
      toDeleteDestination: {
        props: {
          Host: {
            name: ""
          },
          Domains: []
        }
      },
      objects: []
    };
  },
  methods: {
    initSource() {
      return {
        Source: "",
        SrcType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initSourceErrors()
      };
    },
    initDestination() {
      return {
        Destination: "",
        DstType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initDestinationErrors()
      };
    },
    initSourceErrors() {
      return {
        Source: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initDestinationErrors() {
      return {
        Destination: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    filterSrcAuto(query) {
      this.currentSource.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.objects.filter(function(source) {
        return (
          source.type.toLowerCase().includes(query.toLowerCase()) ||
          source.name.toLowerCase().includes(query.toLowerCase()) ||
          source.Description.toLowerCase().includes(query.toLowerCase()) ||
          (source.IpAddress &&
            source.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
          (source.Address &&
            source.Address.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectSrcAuto(item) {
      this.currentSource.Source = item.name;
      this.currentSource.Type = item.type;
      this.currentSource.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
      this.$forceUpdate();
    },
    filterDstAuto(query) {
      this.currentDestination.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.objects.filter(function(source) {
        return (
          source.type.toLowerCase().includes(query.toLowerCase()) ||
          source.name.toLowerCase().includes(query.toLowerCase()) ||
          source.Description.toLowerCase().includes(query.toLowerCase()) ||
          (source.IpAddress &&
            source.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
          (source.Address &&
            source.Address.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectDstAuto(item) {
      this.currentDestination.Destination = item.name;
      this.currentDestination.Type = item.type;
      this.currentDestination.DstType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    getBypasses() {
      var context = this;

      nethserver.exec(
        ["nethserver-suricata/bypass/read"],
        {
          action: "bypass-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.sourceRows = success["sources"];
          context.destinationRows = success["destinations"];

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
            $("#source-tab-parent").click();
          }, 500);

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getObjects() {
      var context = this;

      nethserver.exec(
        ["nethserver-suricata/bypass/read"],
        {
          action: "object-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.objects = success["objects"];
        },
        function(error) {
          console.error(error);
        }
      );
    },
    openCreateSource() {
      this.currentSource = this.initSource();
      $("#createSourceModal").modal("show");
    },
    openCreateDestination() {
      this.currentDestination = this.initDestination();
      $("#createDestinationModal").modal("show");
    },
    openEditSource(source) {
      this.currentSource = JSON.parse(JSON.stringify(source));
      this.currentSource.Source = this.currentSource.props.Host.name;
      this.currentSource.Type = this.currentSource.props.Host.type;
      this.currentSource.SrcType = this.currentSource.props.Host.type;
      this.currentSource.Description = this.currentSource.props.Description;
      this.currentSource.errors = this.initSourceErrors();
      this.currentSource.isEdit = true;
      $("#createSourceModal").modal("show");
    },
    openEditDestination(dest) {
      this.currentDestination = JSON.parse(JSON.stringify(dest));
      this.currentDestination.Destination =
        (this.currentDestination.props.Host &&
          this.currentDestination.props.Host.name) ||
        this.currentDestination.props.Domains.join(",");
      this.currentDestination.Type = this.currentDestination.props.Host.type;
      this.currentDestination.DstType = this.currentDestination.props.Host.type;
      this.currentDestination.Description = this.currentDestination.props.Description;
      this.currentDestination.errors = this.initDestinationErrors();
      this.currentDestination.isEdit = true;
      $("#createDestinationModal").modal("show");
    },
    openDeleteSource(source) {
      this.toDeleteSource = JSON.parse(JSON.stringify(source));
      $("#deleteSourceModal").modal("show");
    },
    openDeleteDestination(dest) {
      this.toDeleteDestination = JSON.parse(JSON.stringify(dest));
      $("#deleteDestinationModal").modal("show");
    },
    saveSource(source) {
      var context = this;

      var sourceObj = {
        action: source.isEdit ? "update-bypass" : "create-bypass",
        Host: {
          type: source.Type || "raw",
          name: source.Source
        },
        Description: source.Description,
        name: source.isEdit ? source.name : null,
        type: "bypass-src"
      };

      context.currentSource.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-suricata/bypass/validate"],
        sourceObj,
        null,
        function(success) {
          context.currentSource.isLoading = false;
          $("#createSourceModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "bypass.source_" +
              (context.currentSource.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "bypass.source_" +
              (context.currentSource.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (source.isEdit) {
            nethserver.exec(
              ["nethserver-suricata/bypass/update"],
              sourceObj,
              function(stream) {
                console.info("source", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-suricata/bypass/create"],
              sourceObj,
              function(stream) {
                console.info("source", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentSource.isLoading = false;
          context.currentSource.errors = context.initSourceErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Source";
              }
              context.currentSource.errors[attr.parameter].hasError = true;
              context.currentSource.errors[attr.parameter].message = attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    saveDestination(destination) {
      var context = this;

      var destinationObj = {
        action: destination.isEdit ? "update-bypass" : "create-bypass",
        Host: {
          type: destination.Type || "raw",
          name: destination.Destination
        },
        Description: destination.Description,
        name: destination.isEdit ? destination.name : null,
        type: "bypass-dst"
      };

      context.currentDestination.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-suricata/bypass/validate"],
        destinationObj,
        null,
        function(success) {
          context.currentDestination.isLoading = false;
          $("#createDestinationModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "bypass.destination_" +
              (context.currentDestination.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "bypass.destination_" +
              (context.currentDestination.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (destination.isEdit) {
            nethserver.exec(
              ["nethserver-suricata/bypass/update"],
              destinationObj,
              function(stream) {
                console.info("destination", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-suricata/bypass/create"],
              destinationObj,
              function(stream) {
                console.info("destination", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentDestination.isLoading = false;
          context.currentDestination.errors = context.initDestinationErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Destination";
              }
              context.currentDestination.errors[attr.parameter].hasError = true;
              context.currentDestination.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    deleteSource(source) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.source_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.source_deleted_error"
      );

      $("#deleteSourceModal").modal("hide");
      nethserver.exec(
        ["nethserver-suricata/bypass/delete"],
        {
          name: source.name
        },
        function(stream) {
          console.info("source", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    deleteDestination(destination) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.destination_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.destination_deleted_error"
      );

      $("#deleteDestinationModal").modal("hide");
      nethserver.exec(
        ["nethserver-suricata/bypass/delete"],
        {
          name: destination.name
        },
        function(stream) {
          console.info("destination", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    toggleSourceStatus(source) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.source_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.source_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-suricata/bypass/update"],
        {
          action:
            source.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: source.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    toggleDestinationStatus(destination) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.destination_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.destination_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-suricata/bypass/update"],
        {
          action:
            destination.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: destination.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style scoped>
.proxy-details {
  margin-left: 10px;
}
</style>
