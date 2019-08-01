import {Injectable} from '@angular/core';
import {IClientOptions, MqttClient} from 'mqtt';
import {AsyncSubject, Observable, Subject} from 'rxjs';
import {map} from 'rxjs/operators';

declare let mqtt: {
  connect(brokerUrl?: any, opts?: IClientOptions): MqttClient
};

@Injectable()
export class NgxMqttLiteService {
  client: MqttClient;
  private ready: AsyncSubject<boolean>;

  /**
   * initializa MQTT
   */
  initializa(brokerUrl?: any, opts?: IClientOptions) {
    this.ready = new AsyncSubject();
    this.client = mqtt.connect(brokerUrl, opts);
    this.client.on('connect', () => {
      this.ready.next(null);
      this.ready.complete();
    });
  }

  /**
   * Scope Function
   */
  scope(): Observable<MqttClient> {
    return this.ready.pipe(
      map(() => this.client)
    );
  }

  /**
   * Proxy Listener
   */
  listen(event: string): Observable<any> {
    const listener = new Subject();
    this.client.on(event, (...param) => {
      listener.next(param);
    });
    return listener.asObservable();
  }

  /**
   * Destory
   */
  destory() {
    this.client.end(true);
    if (this.ready) {
      this.ready.unsubscribe();
      this.ready = null;
      this.client = null;
    }
  }
}
