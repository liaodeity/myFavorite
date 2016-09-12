/**
 * Created by shaolei on 16/5/16.
 */
"use strict"
const cp = require('child_process')
const fs = require('fs')
const Url = require('url')
const co = require('co')
const cheerio = require('cheerio')
const sprintf = require( 'sprintf-js' ).sprintf
const utils = require('./utils')
const MysqlConnection = utils.MysqlConnection
const fetchPage = utils.fetchPage
const dbconf = require('./dbconf.json')
const iconv = require( 'iconv-lite' )

//程序入口,接收父进程的任务参数
process.on('message', paramObj=>main(paramObj))

function main( task ) {
  co(function*() {
    //执行监控项目, 获取状态
    task.status = yield fetchPage({url: task.setting.url, saveHtml: true})

    //重定向一次
    let httpcode = task.status.httpcode
    if (httpcode == 301 || httpcode == 302) {
      if (task.status.headers.hasOwnProperty('location')) {
        let location = task.status.headers.location
        // console.log( 'do redirection: ', location )
        task.status = yield fetchPage( {url:location, saveHtml:true} )
      }
    }

    //解析HTML
    parseHtml( task )
    delete task.status.buf

    // //验证模块
    // if( task.setting.module ) {
    //   // validModule( task )
    //   //方式变更为截图对比
    //   yield co.wrap(validModulePic)( task )
    // }

    //验证内链/图片
    if( task.setting.a ) {
      yield validLink( task )
    }

    if( task.setting.img ) {
      validImage( task )
    }

    //删除html字段,便于调试
    delete task.status.html
    // console.log( task )

    // //统计汇总
    // statistic( task )
    
    //报告状态, fetchPage函数不会抛出异常, 保证将结果返回调度进程
    process.send( task )

    //记录监控日志
    let conn = new MysqlConnection(dbconf)
    yield recordTask( conn, task )
    
    // //记录报警日志
    // yield recordAlarm( conn, task )

    yield conn.disconnect()
  })
    .catch( error=>console.log(error) )
    .then( ()=>{
      //TODO 应该确认destroyAll完成后退出
      MysqlConnection.destroyAll()
      process.exit(0)
    })
}

/**
 * 记录监控任务的执行日志
 * @param conn
 * @param record
 * @returns {Promise.<*>}
 */
function recordTask( conn, task ) {
  let year = new Date().getFullYear()
  let month = (new Date().getMonth()+1).toString()
  if( month.length < 2 ) month = '0' + month

  let mainPage = {
    pageid: task.setting.id,
    url:    task.setting.url,
    type:   0,// 0-主页 1-内链 2-图片
    code:   task.status.httpcode,
    cost:   task.status.cost,
    lasttime: task.status.lasttime,
    batch_id: task.batchid
  }

  let sql = `INSERT INTO mnt_monitor_log_${year}${month}( pageid, url, type, code, cost, lasttime, batch_id ) VALUES( :pageid, :url, :type, :code, :cost, :lasttime, :batch_id )`
  let promises = []
  promises.push( conn.execute( sql, mainPage ) )

  if( task.setting.a ) {
    for( let state of task.linkStatus ) {
      let linkState = {
        pageid: task.setting.id,
        url:    state.url,
        type:   1,// 0-主页 1-内链 2-图片
        code:   state.httpcode,
        cost:   state.cost,
        lasttime: state.lasttime,
        batch_id: task.batchid
      }
      promises.push( conn.execute( sql, linkState ) )
    }
  }

  if( task.setting.module ) {
    // let sql = `INSERT INTO mnt_monitor_module_log_${year}${month}( pageid, module_id, assert, lasttime ) VALUES( :pageid, :module_id, :assert, :lasttime)`
    // for( let state of task.moduleRules ) {
    //   let moduleState = {
    //     pageid: state.pageid,
    //     module_id: state.id,
    //     assert: state.result ? 1 : 0,
    //     lasttime: state.time
    //     batch_id: task.batchid
    //   }
    //   promises.push( conn.execute( sql, moduleState ) )
    // }
    //TODO 记录模块图片对比日志
  }

  return Promise.all( promises )
}

function validModule( task ) {
  let $ = cheerio.load( task.status.html, {decodeEntities: false} );
  for( let rule of task.moduleRules ) {
    rule.result = true
    switch( rule.type ) {
      case 'length':
        let length = $(rule.query).length
        if( rule.expect === 'positive' ) {
          rule.result = (length > 0)
          break
        }
        if( !isNaN(rule.expect) ) {
          rule.result = ( length >= rule.expect )
          break
        }
        break
      default:
        break
    }
    rule.time = new Date()
  }
}

function validImage( task ) {
  //暂不实现

}

/**
 *
 * @param task
 * @returns {*|Promise}
 */
function validLink( task ) {
  return co(function* (){
    let links = getLinks( task.status.html, task.setting.url )

    var begin = new Date()

    task.linkStatus = []
    for( let i = 0; i < links.length; ++i ) {
      let item = yield fetchPage( {url:links[i], timeout:10000, saveHtml:false} )//saveHtml:false, html占用内存太大,可能溢出...
      task.linkStatus.push( item )
    }
    var end = new Date()

    // console.log( 'page', task.setting.url, 'total cost', end-begin, 'link amount', links.length )
  })
}

/**
 *
 * @param html
 * @param url
 * @returns {Array}
 */
function getLinks( html, url ) {
  let $ = cheerio.load( html, {decodeEntities: false} );
  let tmpUrl = '';
  let links = $('a').map(function() {
    let href = $(this).attr('href')
    if( !href )
      return null

    href = href.trim()

    //过滤#,javascript
    if( href.startsWith('#') ||
      href.startsWith('javascript')
    ){
      return null
    }

    //过滤函数调用
    let braceStartIndex = href.indexOf( '(' )
    let braceEndIndex = href.indexOf( ')' )
    if( (braceEndIndex >= 0 || braceStartIndex >= 0) && braceStartIndex > braceEndIndex ) {
      return null
    }

    //处理http://, https://, 绝对路径, 相对路径
    tmpUrl = encodeURI( Url.resolve( url, href ) )

    //过滤非zol的链接
    let urlObj = Url.parse( tmpUrl )
    if( urlObj.hostname.indexOf('zol.com.cn') < 0 ) {
      return null
    }
    
    return tmpUrl;
  }).get()
  //去重
  return Array.from( new Set( links ) )
}

function parseHtml( task ) {
  if( task.status.buf ) {
    //获取http头部的字符集
    let charset = null
    let headers = task.status.headers
    if( headers && headers.hasOwnProperty('content-type') ) {
      let reg = /.+?charset=[^\w]?([-\w]+)/i
      let result = reg.exec( headers['content-type'] )
      if( result && result.length >= 2 )
        charset = result[1]
    }

    //获取页面字符集
    let html = task.status.buf.toString()
    //处理<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    //和 <meta charset="utf-8>"
    let reg = /<meta.+?charset=[^\w]?([-\w]+)/i
    let result = reg.exec( html )
    if( result && result.length >= 2 )
      charset = result[1]
    
    //获取页面标题
    if( charset ) {
      html = iconv.decode( task.status.buf, charset );
    }

    let $ = cheerio.load( html, {decodeEntities: false} )
    let title = $('title').html()
    task.status.html = html
    task.status.title = (title === null) ? '' : title
  }else{
    task.status.html = ''
    task.status.title = ''
  }

}