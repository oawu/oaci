<div class='back'>
  <a href="" class='icon-36'>回上一頁</a>
</div>

<form class='form'>
  <label class='row'>
    <b class='need' data-tip='標題'>標題</b>
    <input type='text' placeholder='標題'>
  </label>

  <label class='row'>
    <b class='need' data-tip='標題'>標題</b>
    <input type='date' placeholder='標題'>
  </label>
  
  <label class='row'>
    <b class='need' data-tip='標題'>標題</b>
    <input type='color' placeholder='標題'>
  </label>

  <label class='row'>
    <b class='need' data-tip='分類'>分類</b>
    <select>
      <option>選擇分類</option>
    </select>
  </label>

  <div class='row'>
    <b class='need' data-tip='選擇項目'>選擇項目</b>
    <div class='radios'>
      <label>
        <input type='radio' name='radio' />
        <span></span>
        項目 1
      </label>
      <label>
        <input type='radio' name='radio' />
        <span></span>
        項目 1
      </label>
    </div>
  </div>

  <div class='row'>
    <b class='need' data-tip='勾選項目'>勾選項目</b>
    <div class='checkboxs'>
      <label>
        <input type='checkbox' />
        <span></span>
        項目 1
      </label>
      <label>
        <input type='checkbox' />
        <span></span>
        項目 1
      </label>

    </div>
  </div>
  
  <div class='row min'>
    <b class='need' data-tip='開啟設定'>開啟設定</b>
    <div class='switches'>
      <label>
        <input type='checkbox' />
        <span></span>
      </label>
    </div>
  </div>

  <div class='row'>
    <b class='need' data-tip='勾選項目'>文章內容</b>
    <textarea class='pure' placeholder='asd'></textarea>
  </div>

  <div class='row'>
    <b class='need' data-tip='勾選項目'>文章內容</b>
    <textarea class='ckeditor'></textarea>
  </div>

  <div class='row'>
    <b data-tip='參考鏈結'>參考鏈結</b>
    <div class='multi-datas'>
      
      <div class='datas'>
        <div class='need'>
          <input>
        </div>
        <div class='need'>
          <input type='file'>
        </div>

        <div class='need'>
          <label class='checkbox'>
            <input type='checkbox' />
            <span></span>
            項目 1
          </label>
          <label class='checkbox'>
            <input type='checkbox' />
            <span></span>
            項目 1
          </label>
          <label class='checkbox'>
            <input type='checkbox' />
            <span></span>
            項目 1
          </label>
        </div>

        <div>
          <select>
            <option>aaaa</option>
          </select>
        </div>

        <div class='need'>
          <select>
            <option>aaaa</option>
          </select>
        </div>

        <a class='icon-04'></a>
      </div>

      <div class='btns'>
        <a class='icon-07'>參考鏈結</a>
      </div> 
    </div>
  </div>

  <div class='row'>
    <b class='need' data-tip='標題'>封面</b>
    <div class='drop-img'>
      <img src='' />
      <input type='file' name='images[]' />
    </div>
  </div>

  <div class='row'>
    <b class='need' data-tip='標題'>封面</b>
    <div class='multi-drop-imgs'>
      <div class='drop-img'>
        <img src='' />
        <input type='file' name='images[]' />
        <a class='icon-04'></a>
      </div>
    </div>
  </div>

  <div class='row'>
    <b class='need' data-tip='標題'>位置</b>
    <div class='map-edit'>
      <!-- <input step="0.01"> -->
      <!-- <input step="0.01"> -->
    </div>
  </div>

  <div class='ctrl'>
    <button type='submit'>確定</button>
    <button type='reset'>取消</button>
  </div>
</form>