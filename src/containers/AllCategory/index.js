import React, { useState } from "react";
import { StyleSheet, View, Text, ScrollView, TouchableOpacity, FlatList, Image } from "react-native";
import { List } from 'react-native-paper';
import { useNavigation } from '@react-navigation/native';
import axios from "axios";
import baseUrl from "../../../assets/common/baseUrl";


const AccordList = ({ subCategory }) => {
    const [expanded, setExpanded] = React.useState(false);

    const handlePress = () => setExpanded(!expanded);
    const navigation = useNavigation();
    return (
        <List.Accordion

            title={subCategory.name}
            expanded={expanded}
            onPress={handlePress}>
            {subCategory.childs.map(e => (
                <TouchableOpacity
                    key={e.id}
                    onPress={() => navigation.navigate('Category', { title: e.name, isfor: 'Ccategory', id: e.id })}>
                    <List.Item title={e.name} />
                </TouchableOpacity>
            ))}
        </List.Accordion>);
}

const CategoryScreen = () => {
    const [category, setCategory] = React.useState([])
    const [subCategory, setsubCategory] = React.useState([])
    const [chilCategory, setChildCategory] = React.useState([])
    React.useEffect(() => {
        axios
            .get(`${baseUrl}categories`)
            .then((res) => {
                setCategory(res.data.Data)
                // console.log(res.data.Data)
                if (res.data.Data[0]) {
                    setStatus(res.data.Data[0].id)
                    setsubCategory(res.data.Data[0].subs)
                }
            })
            .catch((error) => {
                console.log("something wrong")
            })
    }, [])

    const [status, setStatus] = useState(category[0] && category[0].id)

    const setStatusFilter = sub => {
        setStatus(sub.id)
        setsubCategory(sub.subs)
    }

    const renderItem = ({ item, index }) => {

        return (
            <View key={item.id} style={{ backgroundColor: '#fff' }}>
                <List.Section style={{ margin: 3, marginBottom: 0, marginTop: 0 }} >
                    <AccordList subCategory={item} />
                </List.Section>
            </View>
        )
    }

    const separator = () => {
        return <View style={{ height: 1, backgroundColor: 'black' }}></View>
    }

    return (
        <View style={styles.container}>
            
                <View style={{ width: 100, }}>
                <ScrollView>
                    {
                        category.map(e => (

                            <TouchableOpacity
                                style={[styles.mainView, status === e.id && styles.btnTabactive]}
                                onPress={() => setStatusFilter(e)}>
                                <Image source={{ uri: `https://ecpmarket.mywheels.pk/assets/images/categories/${e.app_photo}` }}
                                    resizeMode="contain" style={styles.mainviewimage}></Image>
                                <Text style={[styles.categorytxt]}>{e.name}</Text>
                            </TouchableOpacity>
                        ))
                    }
            </ScrollView>

                </View>

            <FlatList
                numColumns={1}
                data={subCategory}
                keyExtractor={(e, i) => i.toString()}
                renderItem={renderItem}
                ItemSeparatorComponent={separator}
            />
        </View>
    )
}


const styles = StyleSheet.create({
    container: {
        flex: 1,
        flexDirection: "row"
    },
    mainView: {
        paddingLeft: 10,
        paddingTop: 15,
        height: 90,
        backgroundColor: '#EEEEEE',
        fontWeight: '500',
        justifyContent: 'center',
        alignItems: 'center'
    },
    mainviewimage: {
        width: 30,
        height: 30,
    },
    listviewimage: {
        width: 65,
        height: 50,
    },
    listView2: {
        justifyContent: 'space-around'
    },
    categorytxt: {
        paddingHorizontal: 15,
        fontSize: 11,
        textAlign: 'center'
    },
    btnTabactive: {
        backgroundColor: '#fff'
    },
    textTabactive: {
        color: 'red'
    },
    itemName: {
        fontSize: 12,
        width: 65,
        marginTop: 4,
        justifyContent: 'center',
        backgroundColor: '#fff',
    },
});

export default CategoryScreen;