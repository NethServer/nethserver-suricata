<template>
  <div>
    <h2>{{ $t('bypass.title') }}</h2>

    <div v-show="!view.isLoaded" class="spinner spinner-lg"></div>

    <div v-if=" view.isLoaded">
      <h3>{{$t('actions')}}</h3>
      <button @click="openCreateBypass()" class="btn btn-primary btn-lg">{{$t('bypass.add_bypass')}}</button>

      <h3>{{$t('list')}}</h3>
      <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
      <vue-good-table
        v-show="view.isLoaded"
        :customRowsPerPageDropdown="[25,50,100]"
        :perPage="25"
        :columns="bypassColumns"
        :rows="bypassRows"
        :lineNumbers="false"
        :sort-options="{
            enabled: true,
            initialSortBy: {field: 'props.Host.name', type: 'asc'},
          }"
        :search-options="{
            enabled: true,
            placeholder: tableLangsTexts.globalSearchPlaceholder,
          }"
        styleClass="table responsive vgt2"
        :pagination-options="{
            enabled: true,
            rowsPerPageLabel: tableLangsTexts.rowsPerPageText,
            nextLabel: tableLangsTexts.nextText,
            prevLabel: tableLangsTexts.prevText,
            ofLabel: tableLangsTexts.ofText,
            dropdownAllowAll: false,
          }"
      >
        <template slot="table-row" slot-scope="props">
          <span
            v-if="props.column.field == 'props.Host.name'"
            :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']"
          >
            <span class="fa fa-user span-right-icon"></span>
            <a
              :class="['mg-left-5', props.row.props.status == 'enabled' ? '' : 'gray']"
              @click="props.row.props.status == 'enabled' ? openEditBypass(props.row) : undefined"
            >
              <strong>{{ props.row.props.Host.name}}</strong>
              <span class="gray mg-left-5">({{ props.row.props.Host.type}})</span>
            </a>
          </span>
          <span
            v-if="props.column.field == 'props.Description'"
            :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']"
          >
            <span>{{props.row.props.Description}}</span>
          </span>
          <span v-if="props.column.field == 'action'">
            <button
              @click="props.row.props.status == 'disabled' ? toggleBypassStatus(props.row) : openEditBypass(props.row)"
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
                  <a @click="toggleBypassStatus(props.row)">
                    <span
                      :class="['fa', props.row.props.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                    ></span>
                    {{props.row.props.status == 'enabled' ? $t('disable') : $t('enable')}}
                  </a>
                </li>
                <li role="presentation" class="divider"></li>
                <li>
                  <a @click="openDeleteBypass(props.row)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </span>
        </template>
      </vue-good-table>
    </div>
    <!-- MODALS -->
    <div class="modal" id="createBypassModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentBypass.isEdit ? $t('bypass.edit_bypass') + ' '+ currentBypass.name : $t('bypass.add_bypass')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveBypass(currentBypass)">
            <div class="modal-body">
              <div :class="['form-group', currentBypass.errors.Bypass.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.bypass')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentBypass.Bypass"
                    :options="autoOptions"
                    :onInputChange="filterBypassAuto"
                    :onItemSelected="selectBypassAuto"
                    :required="!currentBypass.isEdit"
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
                    v-if="currentBypass.BypassType && currentBypass.BypassType.length > 0"
                    class="help-block gray"
                  >{{currentBypass.BypassType}}</span>
                  <span v-if="currentBypass.errors.Bypass.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentBypass.errors.Bypass.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', currentBypass.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('bypass.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentBypass.Description" class="form-control" />
                  <span
                    v-if="currentBypass.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentBypass.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentBypass.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentBypass.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" id="deleteBypassModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('bypass.delete_bypass')}} {{toDeleteBypass.props.Host.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteBypass(toDeleteBypass)">
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
      bypassColumns: [
        {
          label: this.$i18n.t("bypass.bypass"),
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
          field: "action",
          filterable: true,
          sortable: false
        }
      ],
      bypassRows: [],
      currentBypass: this.initBypass(),
      toDeleteBypass: {
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
    initBypass() {
      return {
        Bypass: "",
        BypassType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initBypassErrors()
      };
    },
    initBypassErrors() {
      return {
        Bypass: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    filterBypassAuto(query) {
      this.currentBypass.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.objects.filter(function(bypass) {
        return (
          (bypass.type.toLowerCase().includes(query.toLowerCase()) ||
            bypass.name.toLowerCase().includes(query.toLowerCase()) ||
            (bypass.Description &&
              bypass.Description.toLowerCase().includes(query.toLowerCase())) ||
            (bypass.IpAddress &&
              bypass.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
            (bypass.Address &&
              bypass.Address.toLowerCase().includes(query.toLowerCase()))) &&
          !(bypass.type == "self")
        );
      });
    },
    selectBypassAuto(item) {
      this.currentBypass.Bypass = item.name;
      this.currentBypass.Type = item.type;
      this.currentBypass.BypassType =
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
          context.bypassRows = success["bypasses"];

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
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
    openCreateBypass() {
      this.currentBypass = this.initBypass();
      $("#createBypassModal").modal("show");
    },
    openEditBypass(bypass) {
      this.currentBypass = JSON.parse(JSON.stringify(bypass));
      this.currentBypass.Bypass = this.currentBypass.props.Host.name;
      this.currentBypass.Type = this.currentBypass.props.Host.type;
      this.currentBypass.BypassType = this.currentBypass.props.Host.type;
      this.currentBypass.Description = this.currentBypass.props.Description;
      this.currentBypass.errors = this.initBypassErrors();
      this.currentBypass.isEdit = true;
      $("#createBypassModal").modal("show");
    },
    openDeleteBypass(bypass) {
      this.toDeleteBypass = JSON.parse(JSON.stringify(bypass));
      $("#deleteBypassModal").modal("show");
    },
    saveBypass(bypass) {
      var context = this;

      var bypassObj = {
        action: bypass.isEdit ? "update-bypass" : "create-bypass",
        Host: {
          type: bypass.Type || "raw",
          name: bypass.Bypass
        },
        Description: bypass.Description,
        name: bypass.isEdit ? bypass.name : null,
        type: "bypass"
      };

      // check bypass type
      if (
        bypassObj.Host.type != "raw" &&
        bypassObj.Host.type != "host" &&
        bypassObj.Host.type != "host-group" &&
        bypassObj.Host.type != "cidr" &&
        bypassObj.Host.type != "iprange"
      ) {
        bypassObj.Host.type = "host";
      }

      context.currentBypass.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-suricata/bypass/validate"],
        bypassObj,
        null,
        function(success) {
          context.currentBypass.isLoading = false;
          $("#createBypassModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "bypass.bypass_" +
              (context.currentBypass.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "bypass.bypass_" +
              (context.currentBypass.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (bypass.isEdit) {
            nethserver.exec(
              ["nethserver-suricata/bypass/update"],
              bypassObj,
              function(stream) {
                console.info("bypass", stream);
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
              bypassObj,
              function(stream) {
                console.info("bypass", stream);
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
          context.currentBypass.isLoading = false;
          context.currentBypass.errors = context.initBypassErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Bypass";
              }
              context.currentBypass.errors[attr.parameter].hasError = true;
              context.currentBypass.errors[attr.parameter].message = attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    deleteBypass(bypass) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.bypass_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.bypass_deleted_error"
      );

      $("#deleteBypassModal").modal("hide");
      nethserver.exec(
        ["nethserver-suricata/bypass/delete"],
        {
          name: bypass.name
        },
        function(stream) {
          console.info("bypass", stream);
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
    toggleBypassStatus(bypass) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "bypass.bypass_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "bypass.bypass_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-suricata/bypass/update"],
        {
          action:
            bypass.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: bypass.name
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

.span-right-icon {
  font-size: 15px;
  margin-right: 8px;
}
</style>
