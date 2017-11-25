Spine     = require("spine")
$         = Spine.$
Model     = Spine.Model
Toolbar   = Model.Toolbar   = require('models/toolbar')
Extender  = require('extensions/controller_extender')


class ToolbarView extends Spine.Controller
  
  @extend Extender
  
  template: (items) ->
    $('#toolsTemplate').tmpl items
  
  constructor: ->
    super
    @current = []
    
  elements:
    'li' :  'items'
    
  events:
    'click'   : 'click'
    
  click: (e) ->
    @lastcontrol = $(e.target)
    
  change: (list = []) ->
    if list.length
      tools = Toolbar.filter list
      content = new Array
      $.merge(content, itm.content) for itm in tools
        
      @current = content
      
      # check for callback
      lastItem = list.last()
      @current.cb = lastItem if typeof lastItem is 'function'
      
    @render()
    
  filterTools: (list) ->
    Toolbar.select list
    
  sort: (a, b) ->
    
    
  refresh: ->
    @change()
  
  lock: ->
    @locked = true
    
  unlock: ->
    @locked = false
    
  clear: ->
    @current = []
    @render()
    
  renderSub: ->
    items = @current[..]
    test = ->
      res = []
      name = itm.itemGroup.name
      if typeof name is 'function'
        res.push name.call()
      else
        res.push name
      res[0]
    itm for itm in items when test(itm) is 'Hilfe'
      
  render: (list=@current) ->
    return if @locked
    
    @trigger 'before:refresh', @
    @html @template list
    @current?.cb?()
    @trigger 'refresh', @, @lastcontrol

module?.exports = ToolbarView