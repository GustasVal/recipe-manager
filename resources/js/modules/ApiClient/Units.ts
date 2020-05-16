import ApiClient from "./ApiClient";

export interface Unit {
    id: number;
    name: string;
    name_shortcut: string;
    name_plural: string;
    name_plural_shortcut: string;
}

export default class Units extends ApiClient {
    protected url = "/units";

    public async index(): Promise<Unit[]> {
        return super.index() as Promise<Unit[]>;
    }
}
