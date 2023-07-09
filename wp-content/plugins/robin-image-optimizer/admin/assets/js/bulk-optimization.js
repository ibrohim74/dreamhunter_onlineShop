function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) {
        return '0 Byte';
    }
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    if (i === 0) {
        return bytes + ' ' + sizes[i];
    }
    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}

jQuery(function ($) {
    var bulkOptimization = {
        inprogress: false,
        serverDown: false,
        i18n: {},
        settings: {},

        init: function () {
            if (wrio_l18n_bulk_page === undefined || wrio_settings_bulk_page === undefined) {
                console.log('[Error]: Required global variables are not declared.');
                return;
            }

            this.i18n = wrio_l18n_bulk_page;
            this.settings = wrio_settings_bulk_page;
            this.startOptButton = $('#wrio-start-optimization');
            this.startConvButton = $('#wrio-start-conversion');

            if ('server_1' === $('#wrio-change-optimization-server').val()) {
                $(".wrio-premium-user-balance-wrap").hide();
                $(".wrio-premium-user-update-wrap").hide();
            }
            if ('server_5' === $('#wrio-change-optimization-server').val()) {
                if (!this.settings.is_premium) {
                    $.wrio_modal.showErrorModal(this.i18n.premium_server_disabled);
                }
            }

            this.registerEvents();
            this.checkServerStatus();
            this.calculateTotalImages();
            this.checkPremiumUserBalance();

        },

        registerEvents: function () {
            var self = this,
                selectedServer = $("#wrio-change-optimization-server option:selected");

            $('#wrio-change-optimization-server').on('change', function () {
                $(this).prop('disabled', true);
                $('.wrio-premium-user-balance').attr('data-server', $(this).val());
                $(".wrio-premium-user-balance-wrap").show();
                $(".wrio-premium-user-update-wrap").show();

                if ('server_1' === $(this).val()) {
                    $(".wrio-premium-user-balance-wrap").hide();
                    $(".wrio-premium-user-update-wrap").hide();
                }
                if ('server_5' === $(this).val()) {
                    if (!self.settings.is_premium) {
                        $.wrio_modal.showErrorModal(self.i18n.premium_server_disabled);
                        selectedServer.prop('selected', true);
                        $(this).prop('disabled', false);

                        return;
                    }
                }

                self.checkPremiumUserBalance();

                selectedServer = $(this).find('option:selected');
                self.checkServerStatus();
            });

            this.startOptButton.on('click', function () {
                self.startOptButton = $(this);

                if ($(this).hasClass('wio-running')) {
                    self.startConvButton.prop('disabled', false);
                    self.stop();
                    return;
                }

                if (self.serverDown) {
                    $.wrio_modal.showErrorModal(self.i18n.server_down_warning);
                    return;
                }

                if ("1" === self.settings.need_migration) {
                    $.wrio_modal.showErrorModal(self.i18n.need_migrations);
                    return;
                }

                if ("0" === self.settings.images_backup) {
                    $.wrio_modal.showWarningModal(self.i18n.process_without_backup, function () {
                        self.showModal();
                    });
                    return;
                }

                self.showModal();

                return false;
            });
        },

        checkPremiumUserBalance: function () {
            var self = this,
                userBalance = $('.wrio-premium-user-balance'),
                balanceResetAt = $('.wrio-premium-user-update'),
                data = {
                    'action': 'wbcr-rio-check-user-balance',
                    '_wpnonce': self.settings.optimization_nonce
                };

            data['server_name'] = $('#wrio-change-optimization-server').val();

            userBalance.addClass('wrio-premium-user-balance-check-proccess');
            userBalance.text('');

            if ("server_5" === data['server_name']) {
                balanceResetAt.addClass('wrio-premium-user-update-check-proccess');
                balanceResetAt.text('');
            }

            $.post(ajaxurl, data, function (response) {
                console.log(response);
                userBalance.removeClass('wrio-premium-user-balance-check-proccess');
                if ("server_5" === data['server_name']) {
                    balanceResetAt.removeClass('wrio-premium-user-update-check-proccess');
                }

                if (!response || !response.data || !response.success) {
                    console.log('[Error]: Response error');
                    response.data && response.data.error && console.log(response.data.error);

                    if (!response || !response.data) {
                        console.log(response);
                    }

                    userBalance.text('error');
                    if ("server_5" === data['server_name']) {
                        balanceResetAt.text('error');
                    }
                } else {
                    userBalance.text(response.data.balance);
                    if ("server_5" === data['server_name']) {
                        balanceResetAt.text(response.data.reset_at);
                    }
                }
            }).fail(function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);

                self.throwError(error);
            });
        },

        checkServerStatus: function () {
            var self = this,
                serverStatus = $('.wrio-server-status'),
                data = {
                    'action': 'wbcr-rio-check-servers-status',
                    '_wpnonce': self.settings.optimization_nonce
                };

            self.serverDown = false;

            data['server_name'] = $('#wrio-change-optimization-server').val();

            serverStatus.addClass('wrio-server-check-proccess');
            serverStatus.text('');
            serverStatus.removeClass('wrio-down').removeClass('wrio-stable');

            self.startOptButton.prop('disabled', true);
            self.startConvButton.prop('disabled', true);

            $.post(ajaxurl, data, function (response) {
                serverStatus.removeClass('wrio-server-check-proccess');
                $('#wrio-change-optimization-server').prop('disabled', false);

                if (!response || !response.data || !response.success) {
                    console.log('[Error]: Response error');
                    response.data && response.data.error && console.log(response.data.error);

                    if (!response || !response.data) {
                        console.log(response);
                    }

                    $('option[name="' + data['server_name'] + '"]').prop('disabled', true);
                    serverStatus.addClass('wrio-down');
                    serverStatus.text(self.i18n.server_status_down);
                    self.serverDown = true;

                    return;
                } else {
                    serverStatus.addClass('wrio-stable');
                    serverStatus.text(self.i18n.server_status_stable);
                }

                self.startOptButton.prop('disabled', false);
                self.startConvButton.prop('disabled', false);

            }).fail(function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);

                self.throwError(error);
            });
        },

        calculateTotalImages: function () {
            var self = this,
                total_num = $('#wio-total-num'),
                data = {
                    'action': 'wbcr-rio-calculate-total-images',
                    '_wpnonce': self.settings.optimization_nonce
                };

            total_num.addClass('wrio-calculate-process');
            total_num.text('');

            $.post(ajaxurl, data, function (response) {
                total_num.removeClass('wrio-calculate-process');

                if (!response || !response.data || !response.success) {
                    console.log('[Error]: Response error');
                    response.data && response.data.error && console.log(response.data.error);

                    if (!response || !response.data) {
                        console.log(response);
                    }

                    total_num.text('');

                    return;
                } else {
                    if (typeof (response.data.total) !== "undefined") {
                        total_num.addClass('wrio-total-images');
                        total_num.text(response.data.total);
                    }
                }
            }).fail(function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);

                self.throwError(error);
            });
        },

        showModal: function () {
            var self = this;
            var infosModal = $('#wrio-tmpl-bulk-optimization');

            if (!infosModal.length) {
                console.log('[Error]: Html template for modal not found.');
                return;
            }

            // Swal Information before loading the optimize process.
            swal({
                title: this.i18n.modal_optimization_title,
                html: infosModal.html(),
                type: '',
                customClass: 'wrio-modal wrio-modal-optimization-way',
                showCancelButton: true,
                showCloseButton: true,
                padding: 0,
                width: 740,
                confirmButtonText: this.i18n.modal_optimization_manual_button,
                cancelButtonText: this.i18n.modal_optimization_cron_button,
                reverseButtons: true,
            }).then(function (result) {

                self.startConvButton.prop('disabled', true);
                self.process();

                window.onbeforeunload = function () {
                    return self.i18n.leave_page_warning;
                }

            }, function (dismiss) {
                if (dismiss === 'cancel') { // you might also handle 'close' or 'timer' if you used those
                    self.startConvButton.prop('disabled', true);
                    self.process('cron');
                } else {
                    throw dismiss;
                }
            });

        },

        /**
         * Start optimization
         * @param {string} type
         */
        process: function (type) {
            var self = this;

            this.inprogress = true;

            var sendData = {
                'action': 'wrio-bulk-optimization-process',
                'scope': this.settings.scope,
                'multisite': 0,
                '_wpnonce': this.settings.optimization_nonce,
            };

            this.setButtonStyleRun(type);

            if ('cron' === type) {
                this.startOptButton.addClass('wrio-cron-mode');

                sendData['action'] = 'wrio-cron-start';

                $.post(ajaxurl, sendData, function (response) {
                    if (!response || !response.success) {
                        console.log('[Error]: Failed ajax request (Start cron).');
                        console.log(sendData);
                        console.log(response);

                        if (response.data && response.data.error_message) {
                            self.throwError(response.data.error_message);
                        }
                    } else {
                        if (response.data && response.data.stop) {
                            self.stop();
                        }
                    }
                }).fail(function (xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);

                    self.throwError(error);
                });

                return;
            }

            this.showMessage(this.i18n.optimization_inprogress.replace("%s", parseInt($('#wio-unoptimized-num').text())));

            // show message: Optimization remined
            /*if( "1" === this.settings.is_network_admin ) {
                sendData['multisite'] = 1;
            }*/

            sendData['reset_current_errors'] = 1;

            this.sendRequest(sendData);
        },

        stop: function () {
            var self = this;

            this.inprogress = false;

            window.onbeforeunload = null;
            self.setButtonStyleStop();
            self.destroyMessages();

            if (this.startOptButton.hasClass('wrio-cron-mode')) {
                this.startOptButton.removeClass('wrio-cron-mode');

                $.post(ajaxurl, {
                    'action': 'wrio-cron-stop',
                    '_wpnonce': self.settings.optimization_nonce,
                    'scope': self.settings.scope
                }, function (response) {
                    if (!response || !response.success) {
                        console.log('[Error]: Failed ajax request (Stop cron).');
                        console.log(response);

                        if (response.data && response.data.error_message) {
                            self.throwError(response.data.error_message);
                        }
                    } else {
                        self.startConvButton.prop('disabled', false);
                    }
                }).fail(function (xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);

                    self.throwError(error);
                });
            }

        },

        complete: function () {
            this.inprogress = false;
            window.onbeforeunload = null;
            this.setButtonStyleComplete();
        },

        setButtonStyleRun: function (mode) {

            this.startOptButton.addClass('wio-running');

            if ("cron" === mode) {
                this.startOptButton.text(this.i18n.modal_optimization_cron_button_stop);
                return;
            }

            this.startOptButton.text(this.i18n.button_stop);
        },

        setButtonStyleComplete: function () {
            this.showMessage(this.i18n.optimization_complete);
            this.startOptButton.text(this.i18n.button_completed);
            this.startOptButton.removeClass('wio-running');
            this.startOptButton.prop('disabled', true);
            this.startConvButton.prop('disabled', false);
        },

        setButtonStyleStop: function () {
            this.startOptButton.removeClass('wio-running');
            this.startOptButton.text(this.i18n.button_start);
        },

        showMessage: function (text) {
            var contanier = $('.wio-page-statistic'),
                message;

            if (contanier.find('.wrio-statistic-message').length) {
                message = contanier.find('.wrio-statistic-message');
            } else {
                message = $('<div>');
                message.addClass('wrio-statistic-message');
                contanier.append(message);
            }

            message.html(text);
        },

        throwError: function (error_message) {
            this.stop();

            var noticeId = $.wbcr_factory_templates_110.app.showNotice(error_message, 'danger');

            setTimeout(function () {
                $.wbcr_factory_templates_110.app.hideNotice(noticeId);
            }, 10000);
        },

        destroyMessages: function () {
            $('.wio-page-statistic').find('.wrio-statistic-message').empty();
        },

        sendRequest: function (data) {
            var self = this;

            if (!this.inprogress) {
                return;
            }

            $.post(ajaxurl, data, function (response) {
                if (!self.inprogress) {
                    return;
                }

                if (!response || !response.success) {
                    console.log('[Error]: Failed ajax request (Try to optimize images).');
                    console.log(response);

                    if (response.data && response.data.error_message) {
                        self.throwError(response.data.error_message);
                    }

                    return;
                }

                data.reset_current_errors = 0;

                if (!response.data.end) {
                    $('#wio-total-unoptimized').text(parseInt(response.data.remain));
                    self.showMessage(self.i18n.optimization_inprogress.replace("%s", parseInt(response.data.remain)));
                    self.sendRequest(data);
                } else {
                    $('#wio-total-unoptimized').text(response.data.remain);
                    self.complete();

                    // если мультисайт режим, то не скрываем кнопку запуска оптимизации
                    /*if( $('#wbcr-rio-current-blog').length ) {
                        $('#wio-start-optimization').toggleClass('wio-running');
                    } else {
                        $('#wio-start-optimization').hide();
                    }*/
                }

                redraw_statistics(response.data.statistic);

                self.updateLog(response.data.last_optimized);
            }).fail(function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);

                self.throwError(error);
            });
        },

        updateLog: function (new_item_data) {
            var self = this;

            var limit = 100,
                tableEl = $('.wrio-optimization-progress .wrio-table');

            if (!tableEl.length || !new_item_data) {
                return;
            }

            // если таблица была пустая
            if ($('.wrio-table-container-empty').length) {
                $('.wrio-table-container-empty').addClass('wrio-table-container').removeClass('wrio-table-container-empty');
                if (tableEl.find('tbody').length) {
                    tableEl.find('tbody').empty();
                }
            }

            $.each(new_item_data, function (index, value) {
                var trEl = $('<tr>'),
                    tdEl = $('<td>'),
                    webpSize = value.webp_size ? value.webp_size : '-';

                if (tableEl.find('.wrio-row-id-' + value.id).length) {
                    tableEl.find('.wrio-row-id-' + value.id).remove();
                }

                trEl.addClass('flash').addClass('wrio-table-item').addClass('wrio-row-id-' + value.id);

                if ('error' === value.type) {
                    trEl.addClass('wrio-error');
                }

                var preview = $('<img width="40" height="40" src="' + value.thumbnail_url + '" alt="">'),
                    previewUrl = $('<a href="' + value.url + '" target="_blank">' + value.file_name + '</a>');

                tableEl.prepend(trEl);

                trEl.append(tdEl.clone().append(preview));
                trEl.append(tdEl.clone().append(previewUrl));

                if ('error' === value.type) {
                    var colspan = value.scope !== 'custom-folders' ? '6' : '5';
                    trEl.append(tdEl.clone().attr('colspan', colspan).text("Error: " + value.error_msg));
                } else {
                    trEl.append(tdEl.clone().text(value.original_size));
                    trEl.append(tdEl.clone().text(value.optimized_size));
                    trEl.append(tdEl.clone().text(webpSize));
                    trEl.append(tdEl.clone().text(value.original_saving));

                    if ("custom-folders" !== self.settings.scope) {
                        trEl.append(tdEl.clone().text(value.thumbnails_count));
                    }

                    trEl.append(tdEl.clone().text(value.total_saving));
                }
            });

            if (tableEl.find('tr').length > limit) {
                var diff = tableEl.find('tr').length - limit;

                for (var i = 0; i < diff; i++) {
                    tableEl.find('tr:last').remove();
                }
            }
        }

    };

    $(document).ready(function () {
        bulkOptimization.init();
        $('[data-toggle="tooltip"]').tooltip();
    });

    var ajaxUrl = ajaxurl;
    var ai_data;

    function redraw_statistics(statistic) {
        $('#wio-main-chart').attr('data-unoptimized', statistic.unoptimized)
            .attr('data-optimized', statistic.optimized)
            .attr('data-errors', statistic.error);
        $('#wio-total-optimized-attachments').text(statistic.optimized); // optimized
        $('#wio-original-size').text(bytesToSize(statistic.original_size));
        $('#wio-optimized-size').text(bytesToSize(statistic.optimized_size));
        $('#wio-total-optimized-attachments-pct').text(statistic.save_size_percent + '%');
        $('#wio-overview-chart-percent').html(statistic.optimized_percent + '<span>%</span>');
        $('.wio-total-percent').text(statistic.optimized_percent + '%');
        $('#wio-optimized-bar').css('width', statistic.percent_line + '%');

        $('#wio-unoptimized-num').text(statistic.unoptimized);
        $('#wio-optimized-num').text(statistic.optimized);
        $('#wio-error-num').text(statistic.error);

        var credits = $('.wrio-premium-user-balance');
        if (credits.attr('data-server') !== "server_5") {
            credits.text(statistic.credits);
        }

        if ($('.wrio-statistic-nav li.active').length) {
            $('.wrio-statistic-nav li.active').find('span.wio-statistic-tab-percent').text(statistic.optimized_percent + '%');
        }

        window.wio_chart.data.datasets[0].data[0] = statistic.unoptimized; // unoptimized
        window.wio_chart.data.datasets[0].data[1] = statistic.optimized; // optimized
        window.wio_chart.data.datasets[0].data[2] = statistic.error; // errors
        window.wio_chart.update();
        if ($('#wio-overview-chart-percent').text() == '100%') {
            window.onbeforeunload = null;
        }
    }

    /*$('#wbcr-rio-current-blog').on('change', function() {
        var self = $(this);
        $('#wio-start-msg-complete').hide();
        $(this).attr('disabled', true);
        $('#wio-start-optimization').attr('disabled', true);
        var ai_data = {
            'action': 'wbcr_rio_update_current_blog',
            'wpnonce': $(this).data('nonce'),
            'current_blog_id': $(this).find('option:selected').val(),
            'context': $(this).attr('data-context')
        };
        $.post(ajaxUrl, ai_data, function(response) {
            self.removeAttr('disabled');
            $('#wio-start-optimization').removeAttr('disabled');
            redraw_statistics(response.data.statistic);
        });
    });*/

});
