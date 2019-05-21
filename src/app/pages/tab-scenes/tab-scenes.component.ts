import {Component} from '@angular/core';
import {CommonService} from 'ngx-bit-lite';

@Component({
  selector: 'app-tab-scenes',
  templateUrl: './tab-scenes.component.html',
  styleUrls: ['./tab-scenes.component.scss']
})
export class TabScenesComponent {
  constructor(private common: CommonService) {
  }

  startOauth() {
    this.common.redirect('https://yelinvan.cc/wechat/oauth');
  }
}
