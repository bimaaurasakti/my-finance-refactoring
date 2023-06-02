"use strict";

const token = localStorage.getItem('token');
const user_id = localStorage.getItem('user_id');

$("#modal-1").fireModal({body: 'Modal body text goes here.'});
$("#modal-2").fireModal({body: 'Modal body text goes here.', center: true});

let modal_3_body = '<p>Object to create a button on the modal.</p><pre class="language-javascript"><code>';
modal_3_body += '[\n';
modal_3_body += ' {\n';
modal_3_body += "   text: 'Login',\n";
modal_3_body += "   submit: true,\n";
modal_3_body += "   class: 'btn btn-primary btn-shadow',\n";
modal_3_body += "   handler: function(modal) {\n";
modal_3_body += "     alert('Hello, you clicked me!');\n"
modal_3_body += "   }\n"
modal_3_body += ' }\n';
modal_3_body += ']';
modal_3_body += '</code></pre>';
$("#modal-3").fireModal({
  title: 'Modal with Buttons',
  body: modal_3_body,
  buttons: [
  {
    text: 'Click, me!',
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
    alert('Hello, you clicked me!');
    }
  }
  ]
});

$("#modal-4").fireModal({
  footerClass: 'bg-whitesmoke',
  body: 'Add the <code>bg-whitesmoke</code> class to the <code>footerClass</code> option.',
  buttons: [
  {
    text: 'No Action!',
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
    }
  }
  ]
});

$("#modal-5").fireModal({
  title: 'Sumber Dana',
  body: $("#modal-source"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
  // Form Data
  let form_data = $(e.target).serialize();

  // DO AJAX HERE
  let fake_ajax = setTimeout(function() {
    form.stopProgress();

    clearInterval(fake_ajax);
  }, 1500);

  e.preventDefault();
  },
  shown: function(modal, form) {
  console.log(form)
  },
  buttons: [
  {
    text: 'Tambah',
    submit: true,
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
    const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
    const token = localStorage.getItem('token');
    $.ajax({
      url: '/api/source',
      headers: { 
        'X-XSRF-TOKEN': csrfToken,
        'Authorization': 'Bearer ' + token,
       },
      method: 'POST',
      dataType: 'json',
      data: {
      "source_name": $('#source_name_create').val(),
      "beginning_balance": $('#beginning_balance').val(),
      "source_user_id": user_id
      },
      success: function(res) {
      $.destroyModal(modal);
      swal('Data berhasil tersimpan', {
          icon: 'success',
      });
      // window.location.reload();
      },
      error: function() {
        $.destroyModal(modal);
        swal('Data tidak bisa tersimpan', {
          icon: 'error',
      });
      // window.location.reload();
      }
    })
    }
  }
  ]
});

$("#modal-6").fireModal({
  body: '<p>Now you can see something on the left side of the footer.</p>',
  created: function(modal) {
  modal.find('.modal-footer').prepend('<div class="mr-auto"><a href="#">I\'m a hyperlink!</a></div>');
  },
  buttons: [
  {
    text: 'No Action',
    submit: true,
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
    }
  }
  ]
});

$("#modal-7").fireModal({
  title: 'Input Transaksi',
  body: $("#form-transaksi"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
  // Form Data
  let form_data = $(e.target).serialize();
  console.log(form_data)

  // DO AJAX HERE
  let fake_ajax = setTimeout(function() {
    form.stopProgress();

    clearInterval(fake_ajax);
  }, 1500);

  e.preventDefault();
  },
  shown: function(modal, form) {
  console.log(form)
  },
  buttons: [
  {
    text: 'Tambah',
    submit: true,
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
      const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];

    $.ajax({
      url: '/api/transaction',
      headers: {
        'X-XSRF-TOKEN': csrfToken,
        'Authorization': 'Bearer ' + token,
      },
      method: 'POST',
      dataType: 'json',
      data: {
        "transaction_type_id": $('#select-tipe-option').val(),
        "transaction_source_id" : $('#select-sumber-dana-option').val(),
        "transaction_date" : $('#tanggal').val(),
        "transaction_total" : $('#jumlah-uang').val(),
        "transaction_description" : $('#keterangan').val(),
        "transaction_user_id" : user_id
      },
      success: function(res) {
      $.destroyModal(modal);
      swal('Data berhasil tersimpan', {
          icon: 'success',
      });
      window.location.reload();
      },
      error: function() {
        $.destroyModal(modal);
        swal('Data tidak bisa tersimpan', {
          icon: 'error',
      });
      window.location.reload();
      }
    })
    }
    }
  ]
});

$("#modal-8").fireModal({
  title: 'Edit Transaksi',
  body: $("#form-edit-transaksi"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
  // Form Data
  let form_data = $(e.target).serialize();
  console.log(form_data)

  // DO AJAX HERE
  let fake_ajax = setTimeout(function() {
    form.stopProgress();
    modal.find('.modal-body').prepend('<div class="alert alert-info">Please check your browser console</div>')

    clearInterval(fake_ajax);
  }, 1500);

  e.preventDefault();
  },
  shown: function(modal, form) {
  console.log(form)
  },
  buttons: [
  {
    text: 'Tambah',
    submit: true,
    class: 'btn btn-primary btn-shadow',
    handler: function(modal) {
    }
  }
  ]
});