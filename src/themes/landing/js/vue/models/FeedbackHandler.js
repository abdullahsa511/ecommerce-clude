export default class FeedbackHandler {
  constructor() {
    this.loading = {};
    this.success = {};
    this.errors = {};
    this.status = {};
    this.message = {};
    this.successTime = 2000;
  }
  hasErrors() {
    return Object.keys(this.errors).length;
  }

  startLoading(type, entity, field) {
    if (entity) {
      this.loading[type] = this.loading[type] || {};
      if (field) {
        this.loading[type][entity] = this.loading[type][entity] || {};
        this.loading[type][entity][field] = true;
      } else {
        this.loading[type][entity] = true;
      }
    } else {
      this.loading[type] = true;
    }
  }
  finishLoading(type, entity, field) {
    if (entity) {
      if (field) {
        delete this.loading[type][entity][field];
      } else {
        delete this.loading[type][entity];
      }
    } else {
      delete this.loading[type];
    }
  }
  showSuccess(type, entity, field, successTime = 0) {
    if (entity) {
      this.success[type] = this.success[type] || {};
      if (field) {
        this.success[type][entity] = this.success[type][entity] || {};
        this.success[type][entity][field] = true;
      } else {
        this.success[type][entity] = true;
      }
    } else {
      this.success[type] = true;
    }
    setTimeout(() => {
      this.removeSuccess(type, entity, field);
    }, successTime??this.successTime);
  }
  removeSuccess(type, entity, field) {
    if (entity) {
      if (field) {
        delete this.success[type][entity][field];
      } else {
        delete this.success[type][entity];
      }
    } else {
      delete this.success[type];
    }
  }
  setMessage(message, type, entity, field) {
    if (entity) {
      this.message[type] = this.message[type] || {};
      if (field) {
        this.message[type][entity] = this.message[type][entity] || {};
        this.message[type][entity][field] = message;
      } else {
        this.message[type][entity] = message;
      }
    } else {
      this.message[type] = message;
    }
    setTimeout(() => {
      this.removeSuccess(type, entity, field);
    }, this.messageTime);
  }
  removeMessage(type, entity, field) {
    if (entity) {
      if (field) {
        delete this.message[type][entity][field];
      } else {
        delete this.message[type][entity];
      }
    } else {
      delete this.message[type];
    }
  }
  setStatus(value, type, entity, field) {
    if (entity) {
      this.status[type] = this.status[type] || {};
      if (field) {
        this.status[type][entity] = this.status[type][entity] || {};
        this.status[type][entity][field] = value;
      } else {
        this.status[type][entity] = value;
      }
    } else {
      this.status[type] = value;
    }
  }
  setError(error, type, entity, field) {
    if (entity) {
      this.errors[type] = this.errors[type] || {};
      if (field) {
        this.errors[type][entity] = this.errors[type][entity] || {};
        this.errors[type][entity][field] = error;
      } else {
        this.errors[type][entity] = error;
      }
    } else {
      this.errors[type] = error;
    }
  }
  
  clearError(type, entity, field) {
    if (entity) {
      if (field) {
        delete this.errors[type]?.[entity][field];
      } else {
        delete this.errors[type][entity];
      }
    } else {
      delete this.errors[type];
    }
  }
}
