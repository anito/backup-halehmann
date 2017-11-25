Spine             = require("spine")
$                 = Spine.$
Flash             = require("models/flash")
User              = require("models/user")

class Login extends Spine.Controller

  elements:
    'form'                      : 'form'
    '.flash'                    : 'flashEl'
    '.status'                   : 'statusEl'
    '#UserPassword'             : 'passwordEl'
    '#UserUsername'             : 'usernameEl'
    '#login .dialogue-content'  : 'contentEl'
    '#loader'                   : 'loader'
    '.guest'                    : 'btnGuest'
    
  events:
    'keypress'          : 'submitOnEnter'
    'click #guestLogin' : 'guestLogin'
    'click #cancel'     : 'cancel'

  flashTemplate: (item) ->
    $('#flashTemplate').tmpl item
    
  statusTemplate: (item) ->
    $('#statusTemplate').tmpl item
    
  constructor: (form) ->
    super
    Flash.fetch()
    flash = Flash.first() if Flash.count()
    @flash flash if flash
    Flash.destroyAll()
    
    @renderGuestLogin()
    
  render: (el, stuff) ->  
    el.html stuff
    @el
    
  submit: =>
    $.ajax
      data: @form.serialize()
      type: 'POST'
      success: @success
      error: @error
      complete: @complete
      
  complete: (xhr) =>
    json = xhr.responseText
    @passwordEl.val('')
    @usernameEl.val('').focus()
    
  success: (json) =>
    json = $.parseJSON json
    User.fetch()
    User.destroyAll()
    user = new User @newAttributes(json)
    user.save()
    @flash json
    fadeFunc = ->
      @contentEl.addClass('fade')
    redirectFunc = ->
      @log 'redirect: admin'+location.hash
      User.redirect ''
    @delay fadeFunc, 2000
    @delay redirectFunc, 2000

  error: (xhr, err) =>
    flash = $.parseJSON(xhr.responseText)
    flash.status = xhr.status
    flash.statusText = xhr.statusText
    @flash flash
    
  flash: (flash) ->
    @oldMessage = @flashEl.html() unless @oldMessage
    delayedFunc = -> @flashEl.html @oldMessage
    @render @flashEl, @flashTemplate flash
    @render @statusEl, @statusTemplate flash
    @delay delayedFunc, 2000
    
  newAttributes: (json) ->
    id: json.id
    username: json.username
    name: json.name
    groupname: json.groupname
    sessionid: json.sessionid
    redirect: location.hash
    
  cancel: (e) ->
    User.redirect()
    e.preventDefault()
    
  renderGuestLogin: ->
    unless Spine.isProduction
      @btnGuest.removeClass('hide')
    else
      @btnGuest.addClass('hide')
    
  guestLogin: ->
    @usernameEl.val('guest')
    @passwordEl.val('guest')
    @submit()
    
  submitOnEnter: (e) ->
    return unless e.keyCode is 13
    @submit()
    e.preventDefault()
    
module?.exports = Login